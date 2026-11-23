<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Room extends Model
{
    public const TERSEDIA = 'tersedia';
    public const TERISI = 'terisi';

    protected $fillable = ['number', 'name', 'slug', 'price', 'status', 'description', 'facilities'];

    protected $casts = [
        'facilities' => 'array',
        'price' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Room $room) {
            if (blank($room->slug)) {
                $room->slug = static::uniqueSlug($room->name, $room->id);
            }
        });

        static::deleting(function (Room $room) {
            foreach ($room->photos as $photo) {
                $photo->deleteFile();
            }
            Storage::disk('public')->deleteDirectory("rooms/{$room->id}");
        });
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::TERSEDIA);
    }

    public function isAvailable(): bool
    {
        return $this->status === self::TERSEDIA;
    }

    public function getPriceFormattedAttribute(): string
    {
        return self::rupiah($this->price);
    }

    public function getCoverUrlAttribute(): ?string
    {
        $photo = $this->relationLoaded('photos') ? $this->photos->first() : $this->photos()->first();

        return $photo?->url;
    }

    /** Fasilitas kamar sebagai [label, icon] mengikuti config/kos.php */
    public function facilityList(): array
    {
        $all = config('kos.facilities');
        $keys = $this->facilities ?: array_keys($all);

        return collect($keys)->filter(fn ($k) => isset($all[$k]))->map(fn ($k) => $all[$k])->values()->all();
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kamar';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public static function rupiah(int $amount): string
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }
}
