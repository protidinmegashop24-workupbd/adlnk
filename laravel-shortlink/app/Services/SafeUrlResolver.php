<?php

namespace App\Services;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Facades\Http;

/**
 * Follows a URL's redirect chain by hand (never letting the HTTP client
 * auto-follow), validating every hop before connecting. This is the only
 * place in the app that makes outbound requests to arbitrary,
 * user-supplied URLs, so it exists specifically to block SSRF: a visitor
 * could otherwise ask the server to "check a link" that actually points
 * at localhost, an internal service, or a cloud metadata endpoint
 * (169.254.169.254).
 */
class SafeUrlResolver
{
    private const MAX_HOPS = 8;
    private const TIMEOUT_SECONDS = 5;
    private const MAX_BODY_BYTES = 1_000_000; // 1 MB — plenty for <head> tags, caps memory/time on huge pages

    /**
     * @return array{hops: array<int, array{url: string, status: ?int}>, final_url: string, status: ?int, error: ?string}
     */
    public function resolve(string $url): array
    {
        $hops = [];
        $current = $url;

        for ($i = 0; $i < self::MAX_HOPS; $i++) {
            if (! preg_match('#^https?://#i', $current)) {
                return $this->result($hops, $current, null, 'Only http:// and https:// links are supported.');
            }

            $host = parse_url($current, PHP_URL_HOST);
            if (! $host) {
                return $this->result($hops, $current, null, 'This does not look like a valid URL.');
            }

            $ips = $this->resolveIps($host);
            if (empty($ips)) {
                return $this->result($hops, $current, null, "Could not find this domain: {$host}.");
            }
            if (! $this->allPublic($ips)) {
                return $this->result($hops, $current, null, 'This link points to a private or disallowed address.');
            }

            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->connectTimeout(self::TIMEOUT_SECONDS)
                    ->withUserAgent('klikwit-link-tools/1.0')
                    ->withOptions(['allow_redirects' => false, 'stream' => true])
                    ->get($current);
            } catch (\Throwable $e) {
                return $this->result($hops, $current, null, 'Could not connect to this link.');
            }

            $status = $response->status();
            $hops[] = ['url' => $current, 'status' => $status];

            // Release the connection without downloading the body — we only need headers.
            try {
                $response->toPsrResponse()->getBody()->close();
            } catch (\Throwable $e) {
                // ignore — best-effort cleanup
            }

            if (in_array($status, [301, 302, 303, 307, 308], true) && $response->header('Location')) {
                $current = (string) UriResolver::resolve(new Uri($current), new Uri($response->header('Location')));

                continue;
            }

            return $this->result($hops, $current, $status, null);
        }

        return $this->result($hops, $current, null, 'Too many redirects.');
    }

    private function result(array $hops, string $finalUrl, ?int $status, ?string $error): array
    {
        return ['hops' => $hops, 'final_url' => $finalUrl, 'status' => $status, 'error' => $error];
    }

    /**
     * Safely follows redirects to a final destination (same protections as
     * resolve()) and downloads up to MAX_BODY_BYTES of its HTML body — used
     * by the SEO tools that need to read <head> tags (Meta Tag Checker,
     * Canonical Checker, Open Graph Checker). The IP is re-validated
     * immediately before this second request as a DNS-rebinding guard: a
     * hostile DNS server could otherwise answer public on the first lookup
     * (in resolve()) and private on a second lookup moments later.
     *
     * @return array{html: ?string, final_url: string, status: ?int, error: ?string}
     */
    public function fetchHtml(string $url): array
    {
        $resolved = $this->resolve($url);

        if ($resolved['error']) {
            return ['html' => null, 'final_url' => $resolved['final_url'], 'status' => null, 'error' => $resolved['error']];
        }

        if ($resolved['status'] === null || $resolved['status'] >= 400) {
            return ['html' => null, 'final_url' => $resolved['final_url'], 'status' => $resolved['status'], 'error' => "This page returned an error (HTTP {$resolved['status']})."];
        }

        $finalUrl = $resolved['final_url'];
        $host = parse_url($finalUrl, PHP_URL_HOST);
        $ips = $host ? $this->resolveIps($host) : [];

        if (empty($ips) || ! $this->allPublic($ips)) {
            return ['html' => null, 'final_url' => $finalUrl, 'status' => null, 'error' => 'This link points to a private or disallowed address.'];
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->connectTimeout(self::TIMEOUT_SECONDS)
                ->withUserAgent('klikwit-seo-tools/1.0')
                ->withOptions(['allow_redirects' => false, 'stream' => true])
                ->get($finalUrl);
        } catch (\Throwable $e) {
            return ['html' => null, 'final_url' => $finalUrl, 'status' => null, 'error' => 'Could not connect to this link.'];
        }

        $contentType = (string) $response->header('Content-Type');
        if ($contentType !== '' && ! str_contains(strtolower($contentType), 'html')) {
            try {
                $response->toPsrResponse()->getBody()->close();
            } catch (\Throwable $e) {
                // ignore
            }

            return ['html' => null, 'final_url' => $finalUrl, 'status' => $response->status(), 'error' => 'This URL does not return an HTML page.'];
        }

        $html = '';

        try {
            $body = $response->toPsrResponse()->getBody();
            while (! $body->eof() && strlen($html) < self::MAX_BODY_BYTES) {
                $chunk = $body->read(8192);
                if ($chunk === '') {
                    break;
                }
                $html .= $chunk;
            }
            $body->close();
        } catch (\Throwable $e) {
            return ['html' => null, 'final_url' => $finalUrl, 'status' => null, 'error' => 'The website took too long to respond or closed the connection.'];
        }

        if ($html === '') {
            return ['html' => null, 'final_url' => $finalUrl, 'status' => $response->status(), 'error' => 'This page returned an empty response.'];
        }

        return ['html' => $html, 'final_url' => $finalUrl, 'status' => $response->status(), 'error' => null];
    }

    /**
     * @return string[] every IP address this host resolves to (empty if it doesn't resolve)
     */
    private function resolveIps(string $host): array
    {
        // parse_url() keeps the brackets on an IPv6 literal host (e.g. "[::1]"),
        // which filter_var() doesn't accept — without stripping them first, no
        // IPv6 literal (public or private) would ever validate here, and it
        // would incorrectly fall through to a DNS lookup of the literal string
        // "[::1]" as if it were a hostname.
        $bareHost = trim($host, '[]');
        if (filter_var($bareHost, FILTER_VALIDATE_IP)) {
            return [$bareHost];
        }

        $ips = [];
        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        foreach ($records as $record) {
            if (! empty($record['ip'])) {
                $ips[] = $record['ip'];
            }
            if (! empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        if (empty($ips)) {
            $resolved = gethostbyname($host);
            if ($resolved !== $host) {
                $ips[] = $resolved;
            }
        }

        return $ips;
    }

    /**
     * True only if every given IP is a public, routable address — rejects
     * loopback, private (RFC1918/RFC4193), and link-local/reserved ranges
     * (which covers the 169.254.169.254 cloud metadata address).
     *
     * @param  string[]  $ips
     */
    private function allPublic(array $ips): bool
    {
        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }
}
