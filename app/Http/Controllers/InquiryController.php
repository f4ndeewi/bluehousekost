<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use App\Models\Room;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request, Room $room)
    {
        $inquiry = Inquiry::create([
            'room_id' => $room->id,
            'name' => $request->input('nama'),
            'whatsapp' => $request->input('whatsapp'),
            'email' => $request->input('email'),
            'type' => $request->input('jenis_pesan'),
            'message' => $request->input('isi_pesan'),
        ]);

        if ($to = config('kos.admin_email')) {
            try {
                Mail::to($to)->send(new InquiryReceived($inquiry->load('room')));
                $inquiry->update(['email_sent' => true]);
            } catch (\Throwable $e) {
                report($e); // pesan tetap tersimpan walau email gagal
            }
        }

        return redirect()
            ->to(route('rooms.show', $room) . '#form-pemesanan')
            ->with('inquiry_sent', true);
    }
}
