<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Jam berapa waktu check-in dan check-out?',
                'answer' => 'Waktu standar check-in kami adalah mulai pukul 14:00 WITA, dan waktu check-out maksimal pukul 12:00 WITA. Jika Anda membutuhkan early check-in atau late check-out, harap hubungi kami sebelumnya (tergantung ketersediaan kamar).',
                'category' => 'Pemesanan',
                'sort_order' => 1,
            ],
            [
                'question' => 'Apakah tersedia layanan antar-jemput?',
                'answer' => 'Saat ini kami tidak menyediakan layanan antar-jemput resmi. Namun, kami bisa membantu mencarikan transportasi lokal jika dibutuhkan.',
                'category' => 'Fasilitas',
                'sort_order' => 2,
            ],
            [
                'question' => 'Metode pembayaran apa saja yang diterima?',
                'answer' => 'Kami menerima pembayaran melalui transfer bank (Virtual Account), e-Wallet (GoPay, OVO, Dana), dan pembayaran tunai di tempat bagi tamu yang melakukan booking langsung di resepsionis.',
                'category' => 'Pembayaran',
                'sort_order' => 3,
            ],
            [
                'question' => 'Apakah sarapan termasuk dalam harga kamar?',
                'answer' => 'Harga kamar yang tertera belum termasuk sarapan (Room Only), namun di sekitar penginapan banyak terdapat warung makan yang bisa dijangkau dengan mudah.',
                'category' => 'Fasilitas',
                'sort_order' => 4,
            ],
            [
                'question' => 'Bagaimana kebijakan pembatalan (cancellation policy) di sini?',
                'answer' => 'Pembatalan dapat dilakukan tanpa biaya jika dilakukan maksimal H-2 sebelum tanggal check-in. Pembatalan setelah itu atau No-Show akan dikenakan biaya 1 malam pertama.',
                'category' => 'Kebijakan',
                'sort_order' => 5,
            ]
        ];

        foreach ($faqs as $faq) {
            \App\Models\Faq::create($faq);
        }
    }
}
