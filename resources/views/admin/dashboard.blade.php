@extends('layouts.admin')

@section('title', 'Dasbor')

@section('content')
<div class="relative mx-auto max-w-page px-5 py-10 md:px-8">
    <div class="orb left-1/4 top-0 h-80 w-96 opacity-70"></div>

    {{-- Header --}}
    <header class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <span class="mb-3 inline-flex items-center gap-2 rounded-full bg-low px-3.5 py-1 shadow-sm">
                <span class="h-2 w-2 animate-pulse rounded-full bg-cyan"></span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-primary">Sistem Pengelolaan Kos Putri</span>
            </span>
            <h1 class="text-[32px] font-extrabold tracking-tight text-navy md:text-[40px]">Dasbor Admin</h1>
            <p class="mt-1 max-w-xl text-muted">Kelola ketersediaan unit kamar kos dan tinjau pesan masuk dari calon penghuni secara langsung.</p>
        </div>
        <div class="bg-grad-deep flex items-center gap-4 rounded-2xl px-5 py-4 text-white shadow-lg">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15"><span class="material-symbols-outlined text-[24px]">bed</span></span>
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-white/70">Okupansi Kamar</span>
                <div class="flex items-baseline gap-2"><span class="text-xl font-extrabold">{{ $available }} Tersedia</span><span class="text-sm text-white/75">/ {{ $occupied }} Terisi</span></div>
            </div>
        </div>
    </header>

    {{-- Data Kamar --}}
    <section id="data-kamar" class="mb-12 scroll-mt-24">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-fixed text-primary"><span class="material-symbols-outlined text-[24px]">door_front</span></span>
                <div>
                    <h2 class="text-xl font-bold text-navy">Data Kamar</h2>
                    <span class="text-[13px] text-muted">Total {{ $rooms->count() }} Kamar</span>
                </div>
            </div>
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary !px-5 !py-2.5"><span class="material-symbols-outlined text-[20px]">add</span> Tambah Kamar</a>
        </div>

        <div class="card overflow-hidden !rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-low text-[11px] font-bold uppercase tracking-wider text-muted">
                        <tr>
                            <th scope="col" class="px-5 py-3.5">Nama Kamar</th>
                            <th scope="col" class="px-5 py-3.5">Harga Bulanan</th>
                            <th scope="col" class="px-5 py-3.5">Status Ketersediaan</th>
                            <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($rooms as $room)
                            <tr class="transition-colors hover:bg-low/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $room->isAvailable() ? 'bg-primary-fixed text-primary' : 'bg-container text-muted' }}">
                                            <span class="material-symbols-outlined text-[20px]">{{ $room->isAvailable() ? 'meeting_room' : 'lock' }}</span>
                                        </span>
                                        <a href="{{ route('rooms.show', $room) }}" target="_blank" class="font-bold text-navy hover:text-primary">{{ $room->name }}</a>
                                    </div>
                                </td>
                                <td class="px-5 py-4"><span class="font-bold text-navy">Rp {{ number_format($room->price, 0, ',', '.') }}</span> <span class="text-[13px] text-muted">/ bln</span></td>
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('admin.rooms.status', $room) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status" class="chip {{ $room->isAvailable() ? 'chip-ok' : 'chip-full' }} cursor-pointer transition hover:brightness-95">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $room->isAvailable() ? 'bg-cyan' : 'bg-slate-400' }}"></span>
                                            {{ $room->isAvailable() ? 'Tersedia' : 'Terisi' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-flex items-center gap-1 rounded-lg bg-primary-fixed px-3 py-1.5 text-[13px] font-bold text-primary transition hover:brightness-95">
                                            <span class="material-symbols-outlined text-[16px]">edit</span> Ubah
                                        </a>
                                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus {{ addslashes($room->name) }}? Foto kamar ikut terhapus dan tindakan ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-[#ffdad6] px-3 py-1.5 text-[13px] font-bold text-[#93000a] transition hover:brightness-95">
                                                <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-muted">Belum ada kamar. Klik "Tambah Kamar" untuk memulai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Pesan Masuk --}}
    <section id="pesan-masuk" class="scroll-mt-24">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-fixed text-primary"><span class="material-symbols-outlined text-[24px]">mark_email_unread</span></span>
                    <div>
                        <h2 class="text-xl font-bold text-navy">Pesan Masuk</h2>
                        <span class="text-[13px] text-muted">{{ $totalInquiries }} pesan @if($unread) · <strong class="text-primary">{{ $unread }} belum dibaca</strong>@endif</span>
                    </div>
                </div>
                <p class="mt-2 text-sm text-muted">Daftar formulir pemesanan dan pertanyaan dari calon penghuni melalui website.</p>
            </div>
            @if ($unread)
                <form method="POST" action="{{ route('admin.inquiries.read-all') }}">
                    @csrf
                    <button class="btn !px-4 !py-2 bg-high text-navy hover:brightness-95"><span class="material-symbols-outlined text-[18px]">done_all</span> Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="card overflow-hidden !rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-low text-[11px] font-bold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-4 py-3.5">Nama</th>
                            <th class="px-4 py-3.5">Nomor WhatsApp</th>
                            <th class="px-4 py-3.5">Email</th>
                            <th class="px-4 py-3.5">Kamar yang Dituju</th>
                            <th class="px-4 py-3.5">Jenis Pesan</th>
                            <th class="px-4 py-3.5">Isi Pesan</th>
                            <th class="px-4 py-3.5">Waktu Kirim</th>
                            <th class="px-4 py-3.5">Notifikasi Email</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($inquiries as $i)
                            <tr class="align-top transition-colors hover:bg-low/60 {{ $i->is_read ? '' : 'bg-primary-fixed/30' }}">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-fixed text-[11px] font-extrabold text-primary">{{ $i->initials }}</span>
                                        <span class="font-bold text-navy">{{ $i->name }}@unless($i->is_read) <span class="ml-1 inline-block h-2 w-2 rounded-full bg-primary align-middle" title="Belum dibaca"></span>@endunless</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <a href="{{ $i->whatsapp_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-semibold text-primary hover:underline">
                                        <span class="material-symbols-outlined text-[16px]">chat</span>{{ $i->whatsapp_display }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-muted">{{ $i->email ?: '—' }}</td>
                                <td class="px-4 py-4">
                                    @if ($i->room)<span class="rounded-md bg-container px-2 py-1 text-[12px] font-bold text-navy">{{ $i->room->name }}</span>@else<span class="text-muted">—</span>@endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="chip {{ $i->type === 'pemesanan' ? 'chip-tag' : 'chip-full' }} !normal-case">
                                        <span class="material-symbols-outlined text-[14px]">{{ $i->type === 'pemesanan' ? 'event_available' : 'help' }}</span> {{ ucfirst($i->type) }}
                                    </span>
                                </td>
                                <td class="max-w-[260px] px-4 py-4">
                                    <p class="msg line-clamp-2 cursor-pointer text-muted" title="Klik untuk membuka/menutup">{{ $i->message ?: '—' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-muted">{{ $i->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</td>
                                <td class="px-4 py-4">
                                    @if ($i->email_sent)
                                        <span class="chip chip-ok !normal-case"><span class="h-1.5 w-1.5 rounded-full bg-cyan"></span> Terkirim</span>
                                    @elseif (config('kos.admin_email'))
                                        <span class="chip chip-err !normal-case"><span class="h-1.5 w-1.5 rounded-full bg-[#ba1a1a]"></span> Gagal</span>
                                    @else
                                        <span class="chip chip-full !normal-case">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-1.5">
                                        <form method="POST" action="{{ route('admin.inquiries.read', $i) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $i->is_read ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-container text-muted transition hover:text-primary">
                                                <span class="material-symbols-outlined text-[18px]">{{ $i->is_read ? 'mark_email_unread' : 'drafts' }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.inquiries.destroy', $i) }}" onsubmit="return confirm('Hapus pesan dari {{ addslashes($i->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus pesan" class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#ffdad6] text-[#93000a] transition hover:brightness-95">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-5 py-10 text-center text-muted">Belum ada pesan masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($inquiries->hasPages())
            <div class="mt-5">{{ $inquiries->fragment('pesan-masuk')->links() }}</div>
        @endif
    </section>
</div>
@endsection
