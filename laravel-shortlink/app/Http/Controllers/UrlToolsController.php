<?php

namespace App\Http\Controllers;

use App\Services\SafeUrlResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UrlToolsController extends Controller
{
    public function __construct(private readonly SafeUrlResolver $resolver) {}

    public function expandPage(): View
    {
        return view('tools.expand');
    }

    public function expand(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
        ]);

        $result = $this->resolver->resolve(trim($data['url']));

        if ($result['error']) {
            return response()->json(['error' => $result['error']], 422);
        }

        return response()->json($result);
    }

    public function checkPage(): View
    {
        return view('tools.check');
    }

    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
        ]);

        $result = $this->resolver->resolve(trim($data['url']));

        if ($result['error']) {
            return response()->json(['ok' => false, 'error' => $result['error']]);
        }

        $ok = $result['status'] !== null && $result['status'] < 400;

        return response()->json([
            'ok' => $ok,
            'status' => $result['status'],
            'final_url' => $result['final_url'],
            'redirect_count' => count($result['hops']) - 1,
        ]);
    }
}
