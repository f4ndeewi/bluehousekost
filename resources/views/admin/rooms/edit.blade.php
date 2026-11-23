@extends('layouts.admin')

@section('title', 'Ubah ' . $room->name)

@section('content')
    @include('admin.rooms.form', [
        'mode' => 'edit',
        'action' => route('admin.rooms.update', $room),
        'heading' => 'Ubah Kamar',
        'badge' => 'Mode Ubah Unit',
        'sub' => 'Perbarui informasi, foto, dan ketersediaan ' . $room->name . '.',
        'unit' => 'Unit #' . str_pad($room->number, 2, '0', STR_PAD_LEFT),
        'submit' => 'Simpan Perubahan',
    ])
@endsection
