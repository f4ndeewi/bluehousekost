<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    private const MAX_PHOTOS = 10;

    public function create()
    {
        $next = (int) Room::max('number') + 1;

        return view('admin.rooms.create', [
            'room' => new Room(['number' => $next, 'name' => "Kamar {$next}", 'status' => Room::TERSEDIA, 'facilities' => array_keys(config('kos.facilities'))]),
        ]);
    }

    public function store(RoomRequest $request)
    {
        $room = DB::transaction(function () use ($request) {
            $number = (int) Room::max('number') + 1;
            $data = $request->validated();

            $room = Room::create([
                'number' => $number,
                'name' => $data['name'],
                'slug' => Room::uniqueSlug($data['name']),
                'price' => $data['price'],
                'status' => $data['status'],
                'description' => $data['description'] ?? null,
                'facilities' => array_values($data['facilities'] ?? []),
            ]);

            $this->syncPhotos($room, $request);

            return $room;
        });

        return redirect()->route('admin.dashboard')->with('success', "{$room->name} berhasil ditambahkan.");
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', ['room' => $room->load('photos')]);
    }

    public function update(RoomRequest $request, Room $room)
    {
        $remaining = $room->photos()->whereNotIn('id', $request->input('delete_photos', []))->count();
        if ($remaining + count($request->file('photos', [])) > self::MAX_PHOTOS) {
            throw ValidationException::withMessages(['photos' => 'Total foto per kamar maksimal ' . self::MAX_PHOTOS . '.']);
        }

        DB::transaction(function () use ($request, $room) {
            $data = $request->validated();

            $room->update([
                'name' => $data['name'],
                'slug' => $room->name === $data['name'] ? $room->slug : Room::uniqueSlug($data['name'], $room->id),
                'price' => $data['price'],
                'status' => $data['status'],
                'description' => $data['description'] ?? null,
                'facilities' => array_values($data['facilities'] ?? []),
            ]);

            $this->syncPhotos($room, $request);
        });

        return redirect()->route('admin.dashboard')->with('success', "{$room->name} berhasil diperbarui.");
    }

    public function destroy(Room $room)
    {
        $name = $room->name;
        $room->delete(); // file foto ikut dibersihkan lewat event deleting di model

        return redirect()->route('admin.dashboard')->with('success', "{$name} berhasil dihapus.");
    }

    /** Ganti cepat Tersedia <-> Terisi dari dasbor. */
    public function toggleStatus(Room $room)
    {
        $room->update(['status' => $room->isAvailable() ? Room::TERISI : Room::TERSEDIA]);

        return back()->with('success', "{$room->name} kini berstatus " . ($room->isAvailable() ? 'Tersedia' : 'Terisi') . '.');
    }

    /** Hapus foto, simpan caption, unggah foto baru, lalu rapikan urutan (sampul = urutan pertama). */
    private function syncPhotos(Room $room, RoomRequest $request): void
    {
        foreach ($room->photos()->whereIn('id', $request->input('delete_photos', []))->get() as $photo) {
            $photo->deleteFile();
            $photo->delete();
        }

        foreach ($request->input('captions', []) as $id => $caption) {
            $room->photos()->whereKey($id)->update(['caption' => $caption ?: null]);
        }

        $order = $room->photos()->count();
        foreach ($request->file('photos', []) as $i => $file) {
            $room->photos()->create([
                'path' => $file->store("rooms/{$room->id}", 'public'),
                'caption' => $order === 0 && $i === 0 ? 'Kamar Utama' : 'Foto ' . ($order + $i + 1),
                'sort_order' => 1000 + $i,
            ]);
        }

        $cover = (int) $request->input('cover_photo');
        $photos = $room->photos()->get()->sort(fn (RoomPhoto $a, RoomPhoto $b) => [$a->id === $cover ? 0 : 1, $a->sort_order, $a->id]
            <=> [$b->id === $cover ? 0 : 1, $b->sort_order, $b->id])->values();
        foreach ($photos as $i => $photo) {
            $photo->update(['sort_order' => $i]);
        }
    }
}
