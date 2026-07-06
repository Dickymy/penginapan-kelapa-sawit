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
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $maxSort = $roomType->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $index => $file) {
            $path = $this->imageService->upload($file, 'room-images');
            $roomType->images()->create([
                'path' => $path,
                'sort_order' => $maxSort + 1 + $index,
            ]);
        }

        return back()->with('success', 'Foto berhasil diunggah.');
    }

    public function setCover(RoomImage $image): RedirectResponse
    {
        // Unset previous cover for this room type
        RoomImage::where('room_type_id', $image->room_type_id)
            ->where('is_cover', true)
            ->update(['is_cover' => false]);

        $image->update(['is_cover' => true]);

        return back()->with('success', 'Cover foto berhasil diubah.');
    }

    public function destroy(RoomImage $image): RedirectResponse
    {
        $this->imageService->delete($image->path);
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
