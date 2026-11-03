<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomImage;
use App\Models\RoomType;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomImageController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function store(Request $request, RoomType $roomType): RedirectResponse
    {
        $maxMb = config('image.upload_max_mb', 15);
        $maxKb = $maxMb * 1024;

        $request->validate([
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', "max:{$maxKb}"],
        ], [
            'images.*.max' => "Ukuran gambar maksimal {$maxMb} MB.",
            'images.*.mimes' => 'Format didukung: JPEG, PNG, WebP.',
        ]);

        $hasCover = $roomType->images()->where('is_cover', true)->exists();
        $maxSort = $roomType->images()->max('sort_order') ?? 0;
        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('images') as $index => $file) {
            try {
                $variants = $this->imageService->uploadWithVariants($file, 'room-images');

                $isCover = (!$hasCover && $uploadedCount === 0);

                $roomType->images()->create([
                    'path' => $variants['original'] ?? $variants['large'],
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'sort_order' => $maxSort + 1 + $index,
                    'is_cover' => $isCover,
                ]);

                if ($isCover) {
                    $hasCover = true;
                }

                $uploadedCount++;
            } catch (\InvalidArgumentException $e) {
                $errors[] = basename($file->getClientOriginalName()) . ': ' . $e->getMessage();
            }
        }

        if ($uploadedCount > 0 && empty($errors)) {
            return back()->with('success', "{$uploadedCount} foto berhasil diunggah dan dioptimasi.");
        }

        if ($uploadedCount > 0 && !empty($errors)) {
            return back()->with('warning', "{$uploadedCount} foto berhasil. " . implode(' ', $errors));
        }

        return back()->with('error', 'Gagal mengunggah: ' . implode(' ', $errors));
    }

    public function setCover(RoomImage $image): RedirectResponse
    {
        // Unset previous cover for this room type
        RoomImage::where('room_type_id', $image->room_type_id)
            ->where('id', '!=', $image->id)
            ->where('is_cover', true)
            ->update(['is_cover' => false]);

        $image->update(['is_cover' => true]);

        return back()->with('success', 'Cover foto berhasil diubah.');
    }

    public function destroy(RoomImage $image): RedirectResponse
    {
        $roomTypeId = $image->room_type_id;
        $wasCover = $image->is_cover;

        // Delete all variant files
        $this->imageService->delete($image->path);

        if ($image->thumb_path) {
            $this->imageService->delete($image->thumb_path);
        }
        if ($image->medium_path) {
            $this->imageService->delete($image->medium_path);
        }
        if ($image->large_path) {
            $this->imageService->delete($image->large_path);
        }

        $image->delete();

        // If the deleted image was cover, promote the first remaining image
        if ($wasCover) {
            $next = RoomImage::where('room_type_id', $roomTypeId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            $next?->update(['is_cover' => true]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
