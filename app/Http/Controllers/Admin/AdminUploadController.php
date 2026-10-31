<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUploadController extends Controller
{
    /**
     * Handle single file upload via AJAX with real-time compression metadata.
     */
    public function store(Request $request, ImageUploadService $imageService): JsonResponse
    {
        $maxMb = config('image.upload_max_mb', 15);

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', "max:" . ($maxMb * 1024)],
            'directory' => ['required', 'string', 'in:galleries,room-images,nearby-places'],
            'variants' => ['nullable', 'boolean'],
        ], [
            'file.required' => 'Pilih gambar untuk diunggah.',
            'file.image' => 'File harus berupa gambar yang valid.',
            'file.mimes' => 'Format gambar yang didukung: JPG, PNG, WEBP.',
            'file.max' => "Ukuran gambar maksimal {$maxMb} MB.",
        ]);

        try {
            $file = $request->file('file');
            $originalSize = (int) $file->getSize();
            $originalName = $file->getClientOriginalName();
            $directory = $request->input('directory');
            $disk = Storage::disk('public');

            if ($request->boolean('variants')) {
                $paths = $imageService->uploadWithVariants($file, $directory);
                $mainPath = $paths['large'] ?? $paths['original'] ?? reset($paths);
                $storedSize = $disk->exists($mainPath) ? (int) $disk->size($mainPath) : $originalSize;
                $savingsPercent = ($originalSize > 0 && $storedSize < $originalSize)
                    ? (int) round((($originalSize - $storedSize) / $originalSize) * 100)
                    : 0;

                return response()->json([
                    'success' => true,
                    'paths' => $paths,
                    'path' => $mainPath,
                    'url' => Storage::url($paths['thumb'] ?? $paths['original'] ?? $mainPath),
                    'size' => $storedSize,
                    'size_formatted' => $this->formatBytes($storedSize),
                    'original_name' => $originalName,
                    'original_size' => $originalSize,
                    'original_size_formatted' => $this->formatBytes($originalSize),
                    'savings_percent' => $savingsPercent,
                ]);
            } else {
                $path = $imageService->upload($file, $directory);
                $storedSize = $disk->exists($path) ? (int) $disk->size($path) : $originalSize;
                $savingsPercent = ($originalSize > 0 && $storedSize < $originalSize)
                    ? (int) round((($originalSize - $storedSize) / $originalSize) * 100)
                    : 0;

                return response()->json([
                    'success' => true,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $storedSize,
                    'size_formatted' => $this->formatBytes($storedSize),
                    'original_name' => $originalName,
                    'original_size' => $originalSize,
                    'original_size_formatted' => $this->formatBytes($originalSize),
                    'savings_percent' => $savingsPercent,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses gambar.'
            ], 422);
        }
    }

    /**
     * Format bytes into a human-readable string.
     */
    private function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = (int) floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

