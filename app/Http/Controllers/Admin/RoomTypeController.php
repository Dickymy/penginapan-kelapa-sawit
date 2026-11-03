<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomTypeRequest;
use App\Http\Requests\Admin\UpdateRoomTypeRequest;
use App\Models\Facility;
use App\Models\RoomType;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function index(): View
    {
        $roomTypes = RoomType::withCount('rooms')
            ->with('images')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        $facilities = Facility::active()->ordered()->get();
        return view('admin.room-types.create', compact('facilities'));
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $roomType = RoomType::create($data);

        if ($request->has('facilities')) {
            $roomType->facilities()->sync($request->input('facilities', []));
        }

        if ($request->has('images')) {
            $hasCover = false;
            foreach ($request->input('images') as $index => $jsonOrPath) {
                $variants = json_decode($jsonOrPath, true);
                if (!$variants || !is_array($variants)) {
                    $variants = ['original' => $jsonOrPath, 'large' => $jsonOrPath];
                }

                $isCover = (!$hasCover && $index === 0);

                $roomType->images()->create([
                    'path' => $variants['original'] ?? $variants['large'] ?? $jsonOrPath,
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'sort_order' => $index,
                    'is_cover' => $isCover,
                ]);

                if ($isCover) {
                    $hasCover = true;
                }
            }
        }

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    public function edit(RoomType $roomType): View
    {
        $facilities = Facility::active()->ordered()->get();
        $roomType->load('facilities', 'images');
        return view('admin.room-types.edit', compact('roomType', 'facilities'));
    }

    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $data = $request->validated();
        $roomType->update($data);

        if ($request->has('facilities')) {
            $roomType->facilities()->sync($request->input('facilities', []));
        }

        if ($request->has('images')) {
            $forceNewCover = $request->boolean('set_new_as_cover');
            $hasCover = $roomType->images()->where('is_cover', true)->exists();
            $maxSort = $roomType->images()->max('sort_order') ?? 0;

            if ($forceNewCover && !empty($request->input('images'))) {
                $roomType->images()->where('is_cover', true)->update(['is_cover' => false]);
                $hasCover = false;
            }

            foreach ($request->input('images') as $index => $jsonOrPath) {
                $variants = json_decode($jsonOrPath, true);
                if (!$variants || !is_array($variants)) {
                    $variants = ['original' => $jsonOrPath, 'large' => $jsonOrPath];
                }

                $isCover = ((!$hasCover || $forceNewCover) && $index === 0);

                $roomType->images()->create([
                    'path' => $variants['original'] ?? $variants['large'] ?? $jsonOrPath,
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'sort_order' => $maxSort + 1 + $index,
                    'is_cover' => $isCover,
                ]);

                if ($isCover) {
                    $hasCover = true;
                    $forceNewCover = false;
                }
            }
        }

        // Ensure at least one image is cover if images exist
        if ($roomType->images()->exists() && !$roomType->images()->where('is_cover', true)->exists()) {
            $roomType->images()->orderBy('sort_order')->orderBy('id')->first()?->update(['is_cover' => true]);
        }

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Tipe kamar berhasil diperbarui.');
    }

    public function toggleActive(RoomType $roomType): RedirectResponse
    {
        $roomType->update(['is_active' => !$roomType->is_active]);

        $status = $roomType->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Tipe kamar berhasil {$status}.");
    }
}
