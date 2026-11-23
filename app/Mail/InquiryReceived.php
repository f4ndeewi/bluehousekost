<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InquiryReceived extends Mailable
{
    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        $room = $this->inquiry->room?->name ?? 'Umum';

        return new Envelope(subject: "[Blue House Kost] {$this->inquiry->type} baru - {$room}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inquiry');
    }
}
