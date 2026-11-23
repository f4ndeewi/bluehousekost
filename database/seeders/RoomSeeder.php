<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Foto contoh dari desain. Ganti dengan foto asli (URL penuh atau path di storage/app/public).
        $covers = [
            'https://lh3.googleusercontent.com/aida-public/AB6AXuANZzOZtbycSwJ-HVCtomFeJiwd9a7idzvLSjfByqY1_JbKrdB975ECUoX3Wjqs4EtZyXyvaes__QYf6USz3Nm5uIV4SElvdTnBBYHFB8ikzVflGT0CfYoL_V0SNWfFfbk3ftxzNGwrFy_KHHslSHWJR4eQaMwQg2nycpLX9Lbkj06HcsBlxzyUiFuv7iidAzKh2FQvJW2c1flWC3RHAL3RTomwupKyz6IR6wG0_p-U',
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBU-v_iyHYswAN136vg4G_rNzi5ZW3HZEOET_jXFDmSy3UcdXCQNn3OLqQFI4Q3pJnYcpWHVNfIT8ok1-x6ZVpbPvsqQ3W-3TifiIYwM144Ei8z-NUMdeSHmDGp06PNLHYTioOxASPTPvppw_QJmwTYFl407dl4CUcUg_jlTAPR7IIgUy066E6rPIkvU4UqRgJJmuoeTUZh795Tp_8TaUMOuru9JtkvDaBxBxYgF5-i',
            'https://lh3.googleusercontent.com/aida-public/AB6AXuArMUoi4YnKptWeMMveShnfdiD2A-guljVEMBj1eUureFTffrk1NkWvlRb_VtWO6lH-WGfcPHG-DE2OEfGKkiWv4fAHJaevNvjTPd9-YFByn8WTFP7USFIHlPAGv36xY1wMj4NHy-a4NZY7HAzsy4ah7f9cbacCxmnPGPHsP7GwYsImHnvMgDnDt24bCL1x3tucJNmnNJuuzinAscMY9bDOWmWdvOaKa4xLnmTDEZvk',
        ];

        $gallery1 = [
            ['https://lh3.googleusercontent.com/aida-public/AB6AXuAeB8CXuWOCxEW5j2kTUIx49ydOpCDVEBKhOyN5rS1jQX5-0LzHQNSXn-urno_UBNrVGh3s2tAYs0URpVZcVXQxz49Z4kDTeGv7gUPjCFV_FXhekjJWUh_uiPSeLJK1ssl1F0lAwo27T_JtEIQm6Ar5ncS8zUs04VOKj_MMRprE6mQ6vpLjk_4241Q_oh19wz0L23RbnOdeqk4vc0rwu7AA57VQzsjkmXcf8xLxr3uBb03Vy1yk8GQKdA', 'Kamar Utama'],
            ['https://lh3.googleusercontent.com/aida/ALJJyrC64ZRtygE8jhiUEJEf6p4sW2QfuPFM0JZiIhL05zJzK308xHjSCNNB4LchhDfkF6uNp9JWCW56IwKOOlpNDAbu_OCYc3PY4HzzneqHXzjZQCMNQ0LGRiFvEPjb9sxsjueB_cDswYchyHprk11YmIusz2HJ8docP-vn01VqBWAjBOesMpi0OlEZRiQKf4dIHVdY1COqsPCiJLp4iwPvLb3f1iEY4LmS9umwgGBzvaRBMA', 'Meja Belajar'],
            ['https://lh3.googleusercontent.com/aida-public/AB6AXuDmCcluEi2KD73e_KlW-pQ5dSbBW1RPeUhbv7Y9SjbNrIrXyg-5nM9MMl3dh3f_WT_FUZkMC5fCz-4o-kf_b5rO3veYUDran-tvD4NeSKBQIfGXNwElxlLq4mykgnLed1z-ug7ee4nR3Z_opb3fn4J5JnzAOIrqlfviajFwjBXw-Wl-0FvVBq6N55iQpoywYmtuYbh6HB6YAjMDN9yTzKGhWIPPwYvdegJEAYVoYpOKBCO5ZFZDM0ADFw', 'Kamar Mandi'],
        ];

        // [nomor, harga, status]
        $data = [
            [1, 750000, Room::TERSEDIA],
            [2, 750000, Room::TERISI],
            [3, 800000, Room::TERSEDIA],
            [4, 800000, Room::TERSEDIA],
            [5, 850000, Room::TERISI],
            [6, 850000, Room::TERSEDIA],
        ];

        foreach ($data as [$n, $price, $status]) {
            $room = Room::updateOrCreate(['number' => $n], [
                'name' => "Kamar {$n}",
                'slug' => "kamar-{$n}",
                'price' => $price,
                'status' => $status,
                'description' => "Kamar {$n} menawarkan suasana yang tenang dan sirkulasi udara yang baik untuk kenyamanan belajar serta istirahat. Dilengkapi dengan perabotan esensial yang tertata rapi khusus untuk mahasiswi dan pekerja muda.",
                'facilities' => array_keys(config('kos.facilities')),
            ]);

            $room->photos()->delete();

            if ($n === 1) {
                foreach ($gallery1 as $i => [$path, $caption]) {
                    $room->photos()->create(['path' => $path, 'caption' => $caption, 'sort_order' => $i]);
                }
            } else {
                $room->photos()->create([
                    'path' => $covers[($n - 1) % 3],
                    'caption' => 'Kamar Utama',
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
