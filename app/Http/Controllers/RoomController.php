<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        // Kamar yang tersedia tampil lebih dulu, lalu yang terisi; di dalam tiap kelompok urut nomor kamar
        $rooms = Room::with('photos')->orderBy('number')->get()
            ->sortBy(fn(Room $r) => [$r->isAvailable() ? 0 : 1, $r->number])
            ->values();

        return view('rooms.index', [
            'rooms' => $rooms,
            'available' => $rooms->where('status', Room::TERSEDIA)->count(),
        ]);
    }

    public function show(Room $room)
    {
        $room->load('photos');

        return view('rooms.show', [
            'room' => $room,
            'gallery' => $room->photos->map(fn($p, $i) => [
                'src' => $p->url,
                'caption' => $p->caption ?: 'Foto ' . ($i + 1),
            ])->values(),
        ]);
    }
}
