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
            foreach ($request->input('images') as $index => $jsonOrPath) {
                $variants = json_decode($jsonOrPath, true);
                if (!$variants || !is_array($variants)) {
                    $variants = ['original' => $jsonOrPath, 'large' => $jsonOrPath];
                }

                $roomType->images()->create([
                    'path' => $variants['original'] ?? $variants['large'] ?? $jsonOrPath,
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'sort_order' => $index,
                    'is_cover' => $index === 0 && $roomType->images()->count() === 0,
                ]);
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
            $maxSort = $roomType->images()->max('sort_order') ?? 0;
            foreach ($request->input('images') as $index => $jsonOrPath) {
                $variants = json_decode($jsonOrPath, true);
                if (!$variants || !is_array($variants)) {
                    $variants = ['original' => $jsonOrPath, 'large' => $jsonOrPath];
                }

                $roomType->images()->create([
                    'path' => $variants['original'] ?? $variants['large'] ?? $jsonOrPath,
                    'thumb_path' => $variants['thumb'] ?? null,
                    'medium_path' => $variants['medium'] ?? null,
                    'large_path' => $variants['large'] ?? null,
                    'sort_order' => $maxSort + 1 + $index,
                ]);
            }
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
