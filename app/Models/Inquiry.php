<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    public const PEMESANAN = 'pemesanan';
    public const PERTANYAAN = 'pertanyaan';

    protected $fillable = ['room_id', 'name', 'whatsapp', 'email', 'type', 'message', 'is_read', 'email_sent'];

    protected $casts = ['is_read' => 'boolean', 'email_sent' => 'boolean'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** 0812-3456-7890 */
    public function getWhatsappDisplayAttribute(): string
    {
        $n = preg_replace('/^(\+62|62)/', '0', $this->whatsapp);

        return trim(preg_replace('/(\d{4})(\d{4})(\d*)/', '$1-$2-$3', $n), '-');
    }

    public function getWhatsappLinkAttribute(): string
    {
        return 'https://wa.me/' . preg_replace('/^(\+62|0)/', '62', $this->whatsapp);
    }

    public function getInitialsAttribute(): string
    {
        return mb_strtoupper(collect(preg_split('/\s+/', trim($this->name)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }
}
