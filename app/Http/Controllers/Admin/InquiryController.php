<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function toggleRead(Inquiry $inquiry)
    {
        $inquiry->update(['is_read' => ! $inquiry->is_read]);

        return back();
    }

    public function markAllRead()
    {
        Inquiry::where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua pesan ditandai sudah dibaca.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return back()->with('success', 'Pesan dihapus.');
    }
}
