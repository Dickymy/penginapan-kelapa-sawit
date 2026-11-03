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
            'images'   => ['required', 'array', 'max:20'],
            'images.*' => ['required', 'string'],
        ], [
            'images.required' => 'Pilih minimal satu gambar untuk diunggah.',
            'images.max'      => 'Maksimal 20 gambar per upload.',
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

                $title   = $request->input("titles.{$index}") ?? $request->input('title');
                $altText = $request->input("alts.{$index}") ?? $request->input('alt_text');

                Gallery::create([
                    'title'               => $title ?: null,
                    'alt_text'            => $altText ?: null,
                    'path'                => $variants['original'] ?? $variants['large'],
                    'thumb_path'          => $variants['thumb']  ?? null,
                    'medium_path'         => $variants['medium'] ?? null,
                    'large_path'          => $variants['large']  ?? null,
                    'is_active'           => true,
                    'sort_order'          => $maxSort + 1 + $index,
                    'created_by_admin_id' => Auth::guard('admin')->id(),
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = 'Gagal memproses gambar ' . ($index + 1) . ': ' . $e->getMessage();
            }
        }

        if ($uploadedCount > 0 && empty($errors)) {
            return back()->with('success', "{$uploadedCount} foto berhasil ditambahkan ke galeri.");
        }
        if ($uploadedCount > 0 && !empty($errors)) {
            return back()->with('warning', "{$uploadedCount} foto berhasil. " . implode(' ', $errors));
        }

        return back()->with('error', 'Gagal menambahkan foto: ' . implode(' ', $errors));
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'title'    => ['nullable', 'string', 'max:191'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $gallery->update([
            'title'    => $request->input('title') ?: null,
            'alt_text' => $request->input('alt_text') ?: null,
        ]);

        return back()->with('success', 'Judul dan keterangan foto berhasil disimpan.');
    }

    /**
     * Replace image file (either via direct file upload or via async upload variants)
     */
    public function replaceImage(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . $this->getMaxKb()],
            'image'      => ['nullable', 'string'],
            'title'      => ['nullable', 'string', 'max:191'],
            'alt_text'   => ['nullable', 'string', 'max:255'],
        ], [
            'image_file.image' => 'File harus berupa gambar yang valid (JPG, PNG, atau WEBP).',
            'image_file.max'   => 'Ukuran foto maksimal ' . config('image.upload_max_mb', 15) . 'MB.',
        ]);

        if (!$request->hasFile('image_file') && !$request->filled('image')) {
            return back()->with('error', 'Silakan pilih file foto pengganti.');
        }

        if ($request->hasFile('image_file')) {
            $variants = $this->imageService->uploadWithVariants($request->file('image_file'), 'galleries');
        } else {
            $variants = json_decode($request->input('image'), true);
            if (!$variants || !is_array($variants)) {
                $variants = ['original' => $request->input('image'), 'large' => $request->input('image')];
            }
        }

        // Delete old variant files
        foreach ([$gallery->path, $gallery->thumb_path, $gallery->medium_path, $gallery->large_path] as $oldPath) {
            if ($oldPath) {
                $this->imageService->delete($oldPath);
            }
        }

        $gallery->update([
            'path'        => $variants['original'] ?? $variants['large'],
            'thumb_path'  => $variants['thumb']   ?? null,
            'medium_path' => $variants['medium']  ?? null,
            'large_path'  => $variants['large']   ?? null,
            'title'       => $request->has('title') ? ($request->input('title') ?: null) : $gallery->title,
            'alt_text'    => $request->has('alt_text') ? ($request->input('alt_text') ?: null) : $gallery->alt_text,
        ]);

        return back()->with('success', 'Foto berhasil diganti dengan gambar baru.');
    }

    public function toggleActive(Gallery $gallery): RedirectResponse
    {
        $gallery->update(['is_active' => !$gallery->is_active]);
        $status = $gallery->is_active ? 'diaktifkan dan tampil di publik' : 'dinonaktifkan (disembunyikan dari publik)';

        return back()->with('success', "Foto berhasil {$status}.");
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer', 'exists:galleries,id'],
        ]);

        foreach ($request->input('order') as $position => $id) {
            Gallery::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return back()->with('success', 'Urutan foto galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        foreach ([$gallery->path, $gallery->thumb_path, $gallery->medium_path, $gallery->large_path] as $path) {
            if ($path) {
                $this->imageService->delete($path);
            }
        }

        $gallery->delete();

        return back()->with('success', 'Foto berhasil dihapus secara permanen.');
    }

    private function getMaxKb(): int
    {
        return config('image.upload_max_mb', 15) * 1024;
    }
}
