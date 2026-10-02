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
     * @return string[] every IP address this host resolves to (empty if it doesn't resolve)
     */
    private function resolveIps(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
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
