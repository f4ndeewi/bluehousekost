@extends('layouts.app')

@section('title', $room->name . ' — ' . config('kos.name'))
@section('description', \Illuminate\Support\Str::limit($room->description, 150))
@section('og_image', $room->cover_url ?? '')

@php
    $waText = rawurlencode('Halo ' . config('kos.name') . ', saya tertarik dengan ' . $room->name);
    $sent = session('inquiry_sent');
    $invalid = fn ($f) => $errors->has($f) ? 'is-invalid' : '';
@endphp

@section('content')
<div class="relative mx-auto max-w-page px-5 pb-14 md:px-8">
    <div class="orb left-1/4 top-12 h-96 w-96 opacity-70"></div>

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="py-5">
        <ol class="flex items-center gap-1.5 text-sm font-semibold text-muted">
            <li><a href="{{ route('rooms.index') }}" class="flex items-center gap-1 hover:text-primary"><span class="material-symbols-outlined text-[18px]">meeting_room</span> Daftar Kamar</a></li>
            <li class="text-outline"><span class="material-symbols-outlined text-[16px]">chevron_right</span></li>
            <li aria-current="page" class="font-bold text-navy">{{ $room->name }}</li>
        </ol>
    </nav>

    <div class="mt-1 grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
        {{-- Galeri --}}
        <section class="flex flex-col gap-4 lg:col-span-7">
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-2xl bg-container shadow-md">
                @if ($gallery->isNotEmpty())
                    <img id="main-img" src="{{ $gallery[0]['src'] }}" alt="{{ $room->name }}" class="h-full w-full object-cover transition-all duration-300">
                @endif
                <span class="chip chip-tag absolute left-4 top-4 !bg-surface/90 backdrop-blur-md"><span class="h-2 w-2 animate-pulse rounded-full bg-primary"></span> Kos Putri</span>
                @if ($gallery->isNotEmpty())
                    <span class="absolute bottom-4 right-4 flex items-center gap-1.5 rounded-xl border border-white/10 bg-[#0F2A5C]/80 px-3.5 py-1.5 text-[11px] font-bold text-white backdrop-blur-md">
                        <span class="material-symbols-outlined text-[16px] text-cyan-300">photo_camera</span>
                        <span id="photo-label">Foto 1 dari {{ $gallery->count() }}: {{ $gallery[0]['caption'] }}</span>
                    </span>
                @endif
            </div>

            @if ($gallery->count() > 1)
                <div class="grid gap-3" style="grid-template-columns:repeat({{ min($gallery->count(), 4) }},minmax(0,1fr))">
                    @foreach ($gallery as $i => $photo)
                        <button type="button" data-thumb="{{ $i }}" aria-label="Lihat {{ $photo['caption'] }}"
                                class="group relative aspect-[4/3] overflow-hidden rounded-xl bg-container shadow-sm transition-all {{ $i === 0 ? 'ring-2 ring-primary ring-offset-2 ring-offset-surface' : 'opacity-75 hover:opacity-100' }}">
                            <img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <span class="pointer-events-none absolute bottom-1.5 left-2 rounded-md bg-[#0F2A5C]/70 px-2 py-0.5 text-[11px] font-semibold text-white backdrop-blur-sm">{{ $photo['caption'] }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Detail --}}
        <section class="flex flex-col justify-between rounded-2xl bg-lowest p-5 shadow-md lg:col-span-5 lg:p-8">
            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <span class="chip {{ $room->isAvailable() ? 'chip-ok' : 'chip-full' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $room->isAvailable() ? 'bg-sky-600' : 'bg-slate-400' }}"></span>
                        {{ $room->isAvailable() ? 'Tersedia' : 'Terisi' }}
                    </span>
                    <span class="text-[13px] font-semibold tracking-wider text-outline">Unit #{{ str_pad($room->number, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h1 class="mb-2 mt-1 text-[28px] font-extrabold tracking-tight text-navy md:text-[40px] md:leading-[48px]">{{ $room->name }}</h1>
                <div class="mb-4 flex items-baseline gap-1.5">
                    <span class="text-[28px] font-extrabold tracking-tight text-primary">{{ $room->price_formatted }}</span>
                    <span class="text-outline">/ bulan</span>
                </div>
                <div class="mb-5 flex items-start gap-2.5 rounded-xl bg-low p-3">
                    <span class="material-symbols-outlined mt-0.5 shrink-0 text-[20px] text-primary">info</span>
                    <p class="text-sm font-semibold leading-snug text-navy">{{ config('kos.payment_period') }}</p>
                </div>
                @if ($room->description)
                    <p class="mb-6 leading-relaxed text-muted">{{ $room->description }}</p>
                @endif

                @unless ($room->isAvailable())
                    <div class="mb-6 flex items-start gap-2.5 rounded-xl border border-line bg-container p-3 text-sm text-muted">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <p>Kamar ini sedang terisi. Anda tetap bisa mengirim pertanyaan, atau <a class="font-semibold text-primary underline" href="{{ route('rooms.index') }}">lihat kamar lain</a>.</p>
                    </div>
                @endunless

                <div class="mb-3">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-outline">Spesifikasi Unit</span>
                    <h2 class="text-lg font-bold text-navy">Fasilitas</h2>
                </div>
                <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($room->facilityList() as [$label, $icon])
                        <div class="flex items-center gap-2 rounded-xl bg-low/60 p-2 transition-all duration-300 hover:-translate-y-0.5 hover:bg-low hover:shadow-lg hover:shadow-cyan/20">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-lowest text-primary shadow-sm"><span class="material-symbols-outlined text-[20px]">{{ $icon }}</span></span>
                            <span class="text-sm font-semibold text-navy">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-line pt-5">
                <a href="https://wa.me/{{ config('kos.whatsapp') }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full">
                    @include('partials.wa-icon') Chat WhatsApp
                </a>
            </div>
        </section>
    </div>

    {{-- Form pemesanan --}}
    <section id="form-pemesanan" class="mt-14 scroll-mt-24">
        <div class="relative overflow-hidden rounded-2xl bg-lowest p-5 shadow-md md:p-10">
            @if ($sent)
                <div class="pop flex flex-col items-center text-center">
                    <div class="bg-grad mb-4 flex h-20 w-20 items-center justify-center rounded-full text-white shadow-lg">
                        <span class="material-symbols-outlined text-[42px]">check_circle</span>
                    </div>
                    <span class="mb-2 inline-block rounded-full bg-primary-fixed px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-primary">Terkirim Sukses</span>
                    <h3 class="mb-2 text-[22px] font-extrabold tracking-tight text-navy">Pesan Anda berhasil dikirim!</h3>
                    <p class="mb-6 max-w-lg leading-relaxed text-muted">Pengelola <strong class="text-navy">{{ config('kos.name') }}</strong> akan segera menghubungi Anda via WhatsApp untuk konfirmasi {{ $room->name }}.</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="https://wa.me/{{ config('kos.whatsapp') }}?text={{ rawurlencode('Halo ' . config('kos.name') . ', saya sudah mengirim formulir pemesanan ' . $room->name) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                            @include('partials.wa-icon', ['class' => 'w-4 h-4']) Buka WhatsApp Sekarang
                        </a>
                        <a href="{{ route('rooms.show', $room) }}#form-pemesanan" class="btn bg-high text-navy hover:brightness-95">
                            <span class="material-symbols-outlined text-[18px]">restart_alt</span> Kirim Formulir Lain
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-8 max-w-2xl">
                    <span class="mb-1 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-primary">
                        <span class="material-symbols-outlined text-[16px]">edit_note</span> Hubungi Pengelola
                    </span>
                    <h3 class="text-[22px] font-bold tracking-tight text-navy">Pemesanan atau Pertanyaan tentang Kamar Ini</h3>
                    <p class="mt-1.5 text-muted">Silakan isi formulir di bawah ini untuk menanyakan ketersediaan atau informasi lebih lanjut mengenai {{ $room->name }}.</p>
                </div>

                <form id="booking-form" method="POST" action="{{ route('inquiry.store', $room) }}" class="max-w-3xl space-y-5" novalidate>
                    @csrf
                    {{-- honeypot --}}
                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="nama" class="flex items-center justify-between text-sm font-semibold text-navy"><span>Nama Lengkap</span><span class="text-[12px] font-normal text-primary">*Wajib</span></label>
                            <input id="nama" name="nama" type="text" required maxlength="100" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap Anda" pattern="[\p{L}\s.'’\-]+" title="Hanya huruf, tanpa angka" oninput="this.value=this.value.replace(/[^\p{L}\s.'’-]/gu,'')" class="field {{ $invalid('nama') }}" autocomplete="name">
                            @error('nama') <span class="flex items-center gap-1 text-[13px] font-medium text-[#ba1a1a]"><span class="material-symbols-outlined text-[16px]">info</span>{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="whatsapp" class="flex items-center justify-between text-sm font-semibold text-navy"><span>Nomor WhatsApp</span><span class="text-[12px] font-normal text-primary">*Wajib</span></label>
                            <input id="whatsapp" name="whatsapp" type="tel" inputmode="tel" required value="{{ old('whatsapp') }}" placeholder="082336970925" class="field {{ $invalid('whatsapp') }}" autocomplete="tel">
                            @error('whatsapp')
                                <span class="flex items-center gap-1 text-[13px] font-medium text-[#ba1a1a]"><span class="material-symbols-outlined text-[16px]">info</span>{{ $message }}</span>
                            @else
                                <span class="flex items-center gap-1 text-[13px] text-muted"><span class="material-symbols-outlined text-[15px] text-outline">help_outline</span>Nomor WhatsApp harus berupa angka</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-sm font-semibold text-navy">Email <span class="text-[12px] font-normal text-outline">(opsional)</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" class="field {{ $invalid('email') }}" autocomplete="email">
                            @error('email') <span class="flex items-center gap-1 text-[13px] font-medium text-[#ba1a1a]"><span class="material-symbols-outlined text-[16px]">info</span>{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="jenis_pesan" class="text-sm font-semibold text-navy">Jenis Pesan</label>
                            <select id="jenis_pesan" name="jenis_pesan" class="field cursor-pointer {{ $invalid('jenis_pesan') }}">
                                @foreach (['pemesanan' => 'Pemesanan', 'pertanyaan' => 'Pertanyaan'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('jenis_pesan', 'pemesanan') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis_pesan')
                                <span class="text-[13px] font-medium text-[#ba1a1a]">{{ $message }}</span>
                            @else
                                <span class="text-[13px] text-muted">Pilih jenis permohonan Anda ke pengelola</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="isi_pesan" class="text-sm font-semibold text-navy">Isi Pesan</label>
                        <textarea id="isi_pesan" name="isi_pesan" rows="4" maxlength="1000" placeholder="Tuliskan pertanyaan atau rencana tanggal mulai sewa Anda..." class="field resize-y {{ $invalid('isi_pesan') }}">{{ old('isi_pesan') }}</textarea>
                        @error('isi_pesan') <span class="text-[13px] font-medium text-[#ba1a1a]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <button id="btn-submit" type="submit" class="btn btn-primary !px-8 disabled:pointer-events-none disabled:opacity-80">
                            <span class="material-symbols-outlined text-[18px]" id="btn-icon">send</span>
                            <span id="btn-text">Kirim</span>
                        </button>
                        <p class="text-[13px] text-outline">Data Anda aman dan hanya digunakan untuk verifikasi calon penghuni {{ config('kos.name') }}.</p>
                    </div>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script type="application/json" id="gallery-data">@json($gallery)</script>
<script src="{{ asset('js/room-detail.js') }}" defer></script>
@endpush
