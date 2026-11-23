<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RoomPhoto extends Model
{
    protected $fillable = ['room_id', 'path', 'caption', 'sort_order'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Hapus file fisik (abaikan foto berupa URL eksternal). */
    public function deleteFile(): void
    {
        if (! str_starts_with($this->path, 'http')) {
            Storage::disk('public')->delete($this->path);
        }
    }

    public function getUrlAttribute(): string
    {
        return str_starts_with($this->path, 'http') ? $this->path : Storage::disk('public')->url($this->path);
    }
}
