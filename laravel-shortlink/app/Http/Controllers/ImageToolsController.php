<?php

namespace App\Http\Controllers;

use App\Services\ImageCompressorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImageToolsController extends Controller
{
    public function __construct(private readonly ImageCompressorService $compressor) {}

    public function page(): View
    {
        return view('tools.image-compressor');
    }

    public function compress(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5 MB
            'max_width' => ['nullable', 'integer', 'min:50', 'max:10000'],
            'quality' => ['nullable', 'integer', 'min:1', 'max:100'],
            'format' => ['nullable', 'string', 'in:keep,jpeg,png,webp'],
        ]);

        $result = $this->compressor->process(
            $request->file('image'),
            $data['max_width'] ?? null,
            $data['quality'] ?? 80,
            $data['format'] ?? 'keep',
        );

        if ($result['error']) {
            return response()->json(['error' => $result['error']], 422);
        }

        return response()->json([
            'data_url' => 'data:'.$result['mime'].';base64,'.base64_encode($result['data']),
            'extension' => $result['extension'],
            'original_bytes' => $result['original_bytes'],
            'new_bytes' => $result['new_bytes'],
        ]);
    }
}
