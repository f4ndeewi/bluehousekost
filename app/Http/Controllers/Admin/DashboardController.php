<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('number')->get();

        return view('admin.dashboard', [
            'rooms' => $rooms,
            'available' => $rooms->where('status', Room::TERSEDIA)->count(),
            'occupied' => $rooms->where('status', Room::TERISI)->count(),
            'inquiries' => Inquiry::with('room')->latest()->paginate(10),
            'unread' => Inquiry::where('is_read', false)->count(),
            'totalInquiries' => Inquiry::count(),
        ]);
    }
}
