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
        $activeCount = $galleries->where('is_active', true)->count();
        $inactiveCount = $galleries->where('is_active', false)->count();

        return view('admin.galleries.index', compact('galleries', 'activeCount', 'inactiveCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:191'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ], [
            'images.required' => 'Pilih minimal satu gambar.',
            'images.max' => 'Maksimal 10 gambar per upload.',
        ]);

        $maxSort = Gallery::max('sort_order') ?? 0;
        $uploadedCount = 0;
        $errors = [];

        foreach ($request->input('images') as $index => $jsonOrPath) {
            try {
                $variants = json_decode($jsonOrPath, true);
                if (!$variants || !is_array($variants)) {
                    $variants = ['original' => $jsonOrPath, 'large' => $jsonOrPath];
                }

                Gallery::create([
                    'title' => $request->input('title'),
                    'alt_text' => $request->input('alt_text'),
                    'path' => $variants['original'] ?? $variants['large'],
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'is_active' => true,
                    'sort_order' => $maxSort + 1 + $index,
                    'created_by_admin_id' => Auth::guard('admin')->id(),
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = 'Gagal memproses gambar ' . ($index + 1) . ': ' . $e->getMessage();
            }
        }

        if ($uploadedCount > 0 && empty($errors)) {
            return back()->with('success', "{$uploadedCount} foto berhasil ditambahkan.");
        }

        if ($uploadedCount > 0 && !empty($errors)) {
            return back()
                ->with('warning', "{$uploadedCount} foto berhasil ditambahkan. " . implode(' ', $errors));
        }

        return back()->with('error', 'Gagal menambahkan foto: ' . implode(' ', $errors));
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:191'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $gallery->update([
            'title' => $request->input('title'),
            'alt_text' => $request->input('alt_text'),
        ]);

        return back()->with('success', 'Informasi foto diperbarui.');
    }

    public function toggleActive(Gallery $gallery): RedirectResponse
    {
        $gallery->update(['is_active' => !$gallery->is_active]);
        $status = $gallery->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Foto berhasil {$status}.");
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:galleries,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            Gallery::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return back()->with('success', 'Urutan galeri diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        // Delete all variant files
        $this->imageService->delete($gallery->path);

        if ($gallery->thumb_path && $gallery->thumb_path !== $gallery->path) {
            $this->imageService->delete($gallery->thumb_path);
        }
        if ($gallery->medium_path && $gallery->medium_path !== $gallery->path) {
            $this->imageService->delete($gallery->medium_path);
        }
        if ($gallery->large_path && $gallery->large_path !== $gallery->path) {
            $this->imageService->delete($gallery->large_path);
        }

        $gallery->delete();

        return back()->with('success', 'Foto dan seluruh varian berhasil dihapus.');
    }

    private function getMaxKb(): int
    {
        return config('image.upload_max_mb', 15) * 1024;
    }
}
