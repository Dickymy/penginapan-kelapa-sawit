<?php

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // For every room type that has images, ensure exactly one image is marked as cover
        $roomTypes = RoomType::with(['images' => fn($q) => $q->orderBy('sort_order')->orderBy('id')])->get();

        foreach ($roomTypes as $roomType) {
            if ($roomType->images->isEmpty()) {
                continue;
            }

            $coverImages = $roomType->images->where('is_cover', true);

            if ($coverImages->isEmpty()) {
                // Promote the first image to cover
                $firstImage = $roomType->images->first();
                $firstImage->updateQuietly(['is_cover' => true]);
            } elseif ($coverImages->count() > 1) {
                // In case multiple images were marked as cover, keep only the first one
                $keepId = $coverImages->first()->id;
                RoomImage::where('room_type_id', $roomType->id)
                    ->where('id', '!=', $keepId)
                    ->where('is_cover', true)
                    ->update(['is_cover' => false]);
            }
        }
    }

    public function down(): void
    {
        // No reversal needed
    }
};

