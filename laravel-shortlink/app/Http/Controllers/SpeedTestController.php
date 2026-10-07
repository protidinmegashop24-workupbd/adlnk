<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Self-hosted internet speed test: measures the visitor's connection to
 * klikwit's own server (download, upload, latency) — not a globally
 * representative "internet speed" the way a multi-server service like
 * speedtest.net would report, and the tool's own copy says so. All timing
 * happens client-side in JavaScript; these endpoints just move real bytes.
 *
 * Download payloads are randomly generated (incompressible) so gzip
 * middleware or a host's auto-compression can't shrink what's actually
 * sent over the wire and skew the measured speed.
 */
class SpeedTestController extends Controller
{
    // Hard server-side cap regardless of what a client requests, so a
    // modified request can't force an arbitrarily large response.
    private const MAX_DOWNLOAD_BYTES = 10 * 1024 * 1024;

    private const CHUNK_BYTES = 262144; // 256 KB per flushed chunk

    public function page(): View
    {
        return view('tools.speed-test');
    }

    public function download(Request $request): StreamedResponse
    {
        $requested = (int) $request->query('bytes', 4 * 1024 * 1024);
        $size = max(1, min($requested, self::MAX_DOWNLOAD_BYTES));

        return response()->stream(function () use ($size) {
            $remaining = $size;
            while ($remaining > 0) {
                $chunk = min(self::CHUNK_BYTES, $remaining);
                echo random_bytes($chunk);
                $remaining -= $chunk;
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => (string) $size,
            'Cache-Control' => 'no-store',
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        // Timing is entirely client-side (request start to response
        // received) — this just has to actually receive the body and
        // respond quickly, so the round trip reflects real upload time.
        return response()->json(['received' => strlen($request->getContent())]);
    }
}
