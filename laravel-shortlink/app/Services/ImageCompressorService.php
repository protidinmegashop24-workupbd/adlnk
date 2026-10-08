<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Compresses/resizes/converts an uploaded image using PHP's built-in GD
 * extension — no external service, nothing saved to disk beyond the
 * request's own temp upload (which Laravel/PHP clean up automatically).
 * GD ships with PHP itself, so this works on ordinary shared hosting
 * without any extra packages.
 */
class ImageCompressorService
{
    // Guards against decoding a huge image into memory on a host with a
    // tight memory_limit — rejected before GD ever touches the file.
    private const MAX_MEGAPIXELS = 40_000_000;

    private const SUPPORTED_OUTPUT_FORMATS = ['keep', 'jpeg', 'png', 'webp'];

    /**
     * @return array{data: ?string, mime: ?string, extension: ?string, error: ?string, original_bytes: int, new_bytes: ?int}
     */
    public function process(UploadedFile $file, ?int $maxWidth, int $quality, string $outputFormat): array
    {
        $originalBytes = $file->getSize();
        $path = $file->getRealPath();

        if (! in_array($outputFormat, self::SUPPORTED_OUTPUT_FORMATS, true)) {
            $outputFormat = 'keep';
        }

        $info = @getimagesize($path);
        if ($info === false) {
            return $this->errorResult('This doesn\'t look like a valid image file.', $originalBytes);
        }

        [$width, $height, $type] = $info;

        if ($width * $height > self::MAX_MEGAPIXELS) {
            return $this->errorResult('This image is too large to process (too many pixels). Try a smaller image.', $originalBytes);
        }

        $sourceFormat = match ($type) {
            IMAGETYPE_JPEG => 'jpeg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_GIF => 'gif',
            default => null,
        };

        if ($sourceFormat === null) {
            return $this->errorResult('Unsupported image type. Please upload a JPEG, PNG, WebP, or GIF.', $originalBytes);
        }

        if ($outputFormat === 'webp' && ! function_exists('imagewebp')) {
            return $this->errorResult('WebP conversion isn\'t available on this server (GD was built without WebP support).', $originalBytes);
        }

        $image = match ($sourceFormat) {
            'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
            'gif' => @imagecreatefromgif($path),
        };

        if ($image === false) {
            return $this->errorResult('Could not read this image — it may be corrupted.', $originalBytes);
        }

        // Resize proportionally if a max width was given and the image is wider.
        if ($maxWidth !== null && $maxWidth > 0 && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            // Preserve transparency for PNG/WebP instead of flattening it to black.
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $effectiveFormat = $outputFormat === 'keep' ? ($sourceFormat === 'gif' ? 'png' : $sourceFormat) : $outputFormat;

        ob_start();
        $ok = match ($effectiveFormat) {
            'jpeg' => (function () use ($image, $quality) {
                imageinterlace($image, true);

                return imagejpeg($image, null, $quality);
            })(),
            'png' => imagepng($image, null, (int) round((100 - $quality) / 100 * 9)),
            'webp' => imagewebp($image, null, $quality),
            default => false,
        };
        $data = ob_get_clean();
        imagedestroy($image);

        if (! $ok || $data === false || $data === '') {
            return $this->errorResult('Could not generate the output image.', $originalBytes);
        }

        return [
            'data' => $data,
            'mime' => match ($effectiveFormat) {
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
            },
            'extension' => match ($effectiveFormat) {
                'jpeg' => 'jpg',
                'png' => 'png',
                'webp' => 'webp',
            },
            'error' => null,
            'original_bytes' => $originalBytes,
            'new_bytes' => strlen($data),
        ];
    }

    /**
     * @return array{data: null, mime: null, extension: null, error: string, original_bytes: int, new_bytes: null}
     */
    private function errorResult(string $message, int $originalBytes): array
    {
        return ['data' => null, 'mime' => null, 'extension' => null, 'error' => $message, 'original_bytes' => $originalBytes, 'new_bytes' => null];
    }
}
