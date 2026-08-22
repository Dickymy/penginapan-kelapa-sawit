<?php

namespace Database\Seeders;

use App\Models\NearbyPlace;
use Illuminate\Database\Seeder;

class NearbyPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = [
            [
                'name' => 'Danau Semayang',
                'category' => 'Wisata',
                'distance' => '15 km',
                'description' => 'Danau terbesar di Kutai Kartanegara, menawarkan pemandangan sunset yang indah dan habitat lumba-lumba air tawar (Pesut Mahakam).',
                'map_link' => 'https://maps.app.goo.gl/example1',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Rumah Makan Pondok Borneo',
                'category' => 'Kuliner',
                'distance' => '2 km',
                'description' => 'Menyajikan hidangan seafood dan ikan air tawar khas sungai Mahakam dengan bumbu tradisional.',
                'map_link' => 'https://maps.app.goo.gl/example2',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Dayaku Raja',
                'category' => 'Kesehatan',
                'distance' => '5 km',
                'description' => 'Rumah sakit umum daerah dengan fasilitas gawat darurat 24 jam.',
                'map_link' => 'https://maps.app.goo.gl/example3',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Pasar Pagi Kota Bangun',
                'category' => 'Belanja',
                'distance' => '1 km',
                'description' => 'Pusat perbelanjaan tradisional terdekat untuk kebutuhan sehari-hari dan oleh-oleh khas daerah.',
                'map_link' => 'https://maps.app.goo.gl/example4',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Pelabuhan Feri Kota Bangun',
                'category' => 'Transportasi',
                'distance' => '3 km',
                'description' => 'Akses penyeberangan jalur sungai Mahakam untuk mobilitas antar kecamatan.',
                'map_link' => 'https://maps.app.goo.gl/example5',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($places as $place) {
            NearbyPlace::create($place);
        }
    }
}
