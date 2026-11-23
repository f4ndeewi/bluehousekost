<?php

namespace App\Http\Controllers;

use App\Models\Room;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = Room::with('photos')->orderBy('number')->get();

        $min = $rooms->min('price');
        $max = $rooms->max('price');

        // Total sewa satu periode pembayaran (mis. 3 bulan) agar calon penghuni tahu nominal yang dibayar di awal
        $months = (int) config('kos.payment_months', 3);
        $periodRange = $min === null ? null : ($min === $max
            ? Room::rupiah($min * $months)
            : Room::rupiah($min * $months) . ' – ' . Room::rupiah($max * $months));

        // Kamar pilihan: yang tersedia dulu, maksimal 3
        [$open, $full] = $rooms->partition(fn (Room $r) => $r->isAvailable());

        return view('pages.home', [
            'available' => $open->count(),
            'total' => max($rooms->count(), config('kos.total_rooms')),
            'priceRange' => $min === null ? null : ($min === $max
                ? Room::rupiah($min)
                : Room::rupiah($min) . ' – ' . Room::rupiah($max)),
            'periodRange' => $periodRange,
            'paymentMonths' => $months,
            'heroImage' => $rooms->first()?->cover_url,
            'featured' => $open->concat($full)->take(3)->values(),
            'gallery' => $rooms->flatMap(fn (Room $r) => $r->photos->map(fn ($p) => [
                'src' => $p->url,
                'caption' => $r->name . ' · ' . ($p->caption ?: 'Foto'),
            ]))->unique('src')->take(8)->values(),
        ]);
    }
}
