<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Room;
use Illuminate\Database\Seeder;

/** Data contoh dari desain dasbor — hanya dijalankan di environment local. */
class InquirySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Siti Nurhaliza', '081234567890', 'siti.nurhaliza@gmail.com', 1, 'pemesanan', 'Halo admin, saya ingin booking Kamar 1 untuk periode masuk semester ganjil bulan depan.', true, 0],
            ['Amanda Putri', '082198765432', 'amanda.putri@student.ub.ac.id', 3, 'pemesanan', 'Selamat siang, apakah bisa survei kamar 3 besok sore bersama orang tua? Terima kasih.', true, 1],
            ['Nabila Rahmawati', '085711223344', 'nabila.rahma@gmail.com', 4, 'pertanyaan', 'Apakah listrik sudah termasuk dalam harga sewa dan boleh membawa kulkas kecil?', false, 2],
            ['Dewi Anggraini', '081355667788', null, 6, 'pertanyaan', 'Apakah ada jam malam di Blue House Kos?', false, 3],
        ];

        foreach ($rows as [$name, $wa, $email, $room, $type, $msg, $sent, $daysAgo]) {
            Inquiry::create([
                'room_id' => Room::where('number', $room)->value('id'),
                'name' => $name, 'whatsapp' => $wa, 'email' => $email, 'type' => $type, 'message' => $msg,
                'email_sent' => $sent, 'is_read' => $daysAgo > 1,
                'created_at' => now()->subDays($daysAgo)->subHours($daysAgo * 2),
                'updated_at' => now()->subDays($daysAgo),
            ]);
        }
    }
}
