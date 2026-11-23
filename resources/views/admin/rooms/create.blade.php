@extends('layouts.admin')

@section('title', 'Tambah Kamar')

@section('content')
    @include('admin.rooms.form', [
        'mode' => 'create',
        'action' => route('admin.rooms.store'),
        'heading' => 'Tambah Kamar',
        'badge' => 'Mode Tambah Unit',
        'sub' => 'Tambahkan unit kamar baru ke dalam daftar ' . config('kos.name') . '.',
        'unit' => 'Unit Baru (#' . $room->number . ')',
        'submit' => 'Simpan Kamar',
    ])
@endsection
