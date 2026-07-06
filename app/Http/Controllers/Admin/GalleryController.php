<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function index(): View
    {
        $galleries = Gallery::ordered()->get();
        return view('admin.galleries.index', compact('galleries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:2048'],
            'title' => ['nullable', 'string', 'max:191'],
        ]);

        $maxSort = Gallery::max('sort_order') ?? 0;

        foreach ($request->file('images') as $index => $file) {
            $path = $this->imageService->upload($file, 'galleries');
            Gallery::create([
                'title' => $request->input('title'),
                'path' => $path,
                'is_active' => true,
                'sort_order' => $maxSort + 1 + $index,
                'created_by_admin_id' => Auth::guard('admin')->id(),
            ]);
        }

        return back()->with('success', 'Galeri berhasil diunggah.');
    }

    public function toggleActive(Gallery $gallery): RedirectResponse
    {
        $gallery->update(['is_active' => !$gallery->is_active]);
        return back()->with('success', 'Status galeri berhasil diubah.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->imageService->delete($gallery->path);
        $gallery->delete();
        return back()->with('success', 'Galeri berhasil dihapus.');
    }
}
