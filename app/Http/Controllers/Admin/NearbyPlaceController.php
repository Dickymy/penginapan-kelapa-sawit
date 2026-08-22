<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNearbyPlaceRequest;
use App\Http\Requests\UpdateNearbyPlaceRequest;
use App\Models\NearbyPlace;
use App\Services\ImageUploadService;

class NearbyPlaceController extends Controller
{
    public function index()
    {
        $places = NearbyPlace::orderBy('sort_order')->orderBy('id')->paginate(15);
        return view('admin.nearby-places.index', compact('places'));
    }

    public function create()
    {
        return view('admin.nearby-places.create');
    }

    public function store(StoreNearbyPlaceRequest $request, ImageUploadService $imageUploadService)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $imageUploadService->upload($request->file('image'), 'nearby-places');
        }

        NearbyPlace::create($data);

        return redirect()->route('admin.nearby-places.index')->with('success', 'Tempat sekitar berhasil ditambahkan.');
    }

    public function show(NearbyPlace $nearbyPlace)
    {
        return view('admin.nearby-places.show', compact('nearbyPlace'));
    }

    public function edit(NearbyPlace $nearbyPlace)
    {
        return view('admin.nearby-places.edit', compact('nearbyPlace'));
    }

    public function update(UpdateNearbyPlaceRequest $request, NearbyPlace $nearbyPlace, ImageUploadService $imageUploadService)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($nearbyPlace->image) {
                // Delete old variants if needed, or simply let the service handle overwrite/storage.
                // Normally you'd delete old image here if ImageUploadService had a delete method.
                // Assuming we just store the new one.
            }
            $data['image'] = $imageUploadService->upload($request->file('image'), 'nearby-places');
        }

        $nearbyPlace->update($data);

        return redirect()->route('admin.nearby-places.index')->with('success', 'Tempat sekitar berhasil diperbarui.');
    }

    public function destroy(NearbyPlace $nearbyPlace)
    {
        $nearbyPlace->delete();
        return redirect()->route('admin.nearby-places.index')->with('success', 'Tempat sekitar berhasil dihapus.');
    }
}
