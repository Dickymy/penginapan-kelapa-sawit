<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $twin = RoomType::firstOrCreate(
            ['slug' => 'twin'],
            [
                'name' => 'Twin',
                'short_description' => 'Kamar Twin dengan dua tempat tidur',
                'description' => null,
                'capacity' => 2,
                'bed_count' => 2,
                'bed_type' => null,
                'base_price' => 0, // Belum dikonfirmasi
                'is_active' => false, // Inactive sampai admin mengisi data lengkap
                'sort_order' => 1,
            ]
        );

        Room::firstOrCreate(
            ['code' => 'TWIN-01'],
            [
                'room_type_id' => $twin->id,
                'name' => 'Twin 01',
                'status' => 'active',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Room::firstOrCreate(
            ['code' => 'TWIN-02'],
            [
                'room_type_id' => $twin->id,
                'name' => 'Twin 02',
                'status' => 'active',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $this->command->info('Room seeder: Twin type + Twin 01 + Twin 02 created.');
    }
}
