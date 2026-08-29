<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminUploadController extends Controller
{
    /**
     * Handle single file upload via AJAX.
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
            'file.image' => 'File harus berupa gambar.',
            'file.mimes' => 'Format didukung: JPEG, PNG, WebP.',
            'file.max' => "Ukuran gambar maksimal {$maxMb} MB.",
        ]);

        try {
            $directory = $request->input('directory');
            
            if ($request->boolean('variants')) {
                $paths = $imageService->uploadWithVariants($request->file('file'), $directory);
                return response()->json([
                    'success' => true, 
                    'paths' => $paths,
                    'url' => \Storage::url($paths['thumb'] ?? $paths['original'] ?? $paths['large'])
                ]);
            } else {
                $path = $imageService->upload($request->file('file'), $directory);
                return response()->json([
                    'success' => true, 
                    'path' => $path,
                    'url' => \Storage::url($path)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
