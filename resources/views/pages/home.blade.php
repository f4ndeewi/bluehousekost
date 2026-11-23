@extends('layouts.app')

@section('og_image', $heroImage ?? '')

@section('content')
{{-- HERO --}}
<section class="relative overflow-hidden px-5 pb-14 pt-8 md:px-8 md:pt-14">
    <div class="orb -top-24 left-1/2 h-[400px] w-[680px] -translate-x-1/2"></div>
    <div class="orb right-[-5rem] top-1/3 h-[340px] w-[420px] opacity-70"></div>

    <div class="mx-auto max-w-page">
        <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
            <div class="flex flex-col items-start gap-5 lg:col-span-7">
                <h1 class="text-[36px] font-extrabold leading-[44px] tracking-tight text-navy md:text-[56px] md:leading-[64px]">
                    Kos Putri yang <span class="text-grad">Nyaman</span> dan Aman
                </h1>
                <p class="max-w-xl text-lg leading-relaxed text-muted">{{ config('kos.about') }}</p>

                <div class="flex w-full flex-wrap items-center gap-3 pt-1 sm:w-auto">
                    <a href="{{ route('rooms.index') }}" class="btn btn-primary w-full sm:w-auto">
                        Lihat Daftar Kamar <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                    <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline w-full sm:w-auto">
                        @include('partials.wa-icon') Chat WhatsApp
                    </a>
                </div>

                {{-- Room Availability Counter --}}
                <div class="bg-grad-deep inline-flex items-center gap-3 rounded-2xl px-5 py-3 text-white shadow-lg">
                    <span class="relative flex h-3 w-3">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-300 opacity-70"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-cyan-300"></span>
                    </span>
                    <span class="text-sm font-semibold">Tersisa <strong class="text-lg font-extrabold">{{ $available }}</strong> dari {{ $total }} Kamar</span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-line bg-lowest p-3 md:p-5" style="box-shadow:0 20px 40px -15px rgba(15,42,92,.1)">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-container">
                        @if ($heroImage)
                            <img src="{{ $heroImage }}" alt="Tampilan {{ config('kos.name') }}" class="h-full w-full object-cover" loading="eager">
                        @endif

                        <div class="absolute inset-x-3.5 bottom-3.5 flex items-center justify-between rounded-xl border border-white/40 bg-white/90 p-3 backdrop-blur-md">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#dbe1ff] text-[#2563EB]"><span class="material-symbols-outlined text-[18px]">verified_user</span></span>
                                <span>
                                    <span class="block text-[14px] font-bold leading-tight text-[#0F2A5C]">Keamanan &amp; Privasi Terjaga</span>
                                    <span class="block text-[12px] text-slate-600">Akses teratur dan kondusif</span>
                                </span>
                            </div>
                            <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-[11px] font-bold text-[#00687a]">Terbatas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- KAMAR PILIHAN --}}
@if ($featured->isNotEmpty())
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Pilihan Kamar</span>
                <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Kamar Pilihan</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">Lihat semua kamar <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($featured as $room)
                @php $open = $room->isAvailable(); @endphp
                <a href="{{ route('rooms.show', $room) }}" class="card card-hover group flex flex-col overflow-hidden">
                    <div class="relative aspect-[4/3] overflow-hidden bg-container">
                        @if ($room->cover_url)<img src="{{ $room->cover_url }}" alt="{{ $room->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">@endif
                        <span class="chip {{ $open ? 'chip-ok' : 'chip-full' }} absolute left-4 top-4 !bg-lowest/90 backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full {{ $open ? 'animate-pulse bg-cyan' : 'bg-slate-400' }}"></span>{{ $open ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-5">
                        <div>
                            <h3 class="text-lg font-bold text-navy">{{ $room->name }}</h3>
                            <p class="text-sm text-muted"><span class="font-extrabold text-navy">{{ $room->price_formatted }}</span> / bulan</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-fixed text-primary transition-colors group-hover:bg-primary group-hover:text-white"><span class="material-symbols-outlined text-[20px]">arrow_forward</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- GALERI: carousel coverflow 3D --}}
@if ($gallery->isNotEmpty())
@php
    $first = explode(' · ', $gallery[0]['caption'], 2);
    $count = $gallery->count();
@endphp
<section class="reveal overflow-hidden px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mx-auto mb-6 max-w-2xl text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Intip Suasananya</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Galeri Foto</h2>
            <p class="mt-1.5 text-muted">Geser, tekan panah, atau klik foto di tengah untuk memperbesar.</p>
        </div>

        <div class="cf" id="cf" data-autoplay="5200">
            <div class="cf-stage" id="cf-stage" tabindex="0" role="region" aria-roledescription="carousel" aria-label="Galeri foto">
                @foreach ($gallery as $i => $photo)
                    <button type="button" class="cf-slide" data-slide data-src="{{ $photo['src'] }}" data-caption="{{ $photo['caption'] }}" aria-label="Foto {{ $i + 1 }} dari {{ $count }}: {{ $photo['caption'] }}">
                        <img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" loading="{{ $i < 3 ? 'eager' : 'lazy' }}" draggable="false">
                        <span class="cf-shade" aria-hidden="true"></span>
                        <span class="cf-num" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="cf-zoom material-symbols-outlined" aria-hidden="true">zoom_in</span>
                    </button>
                @endforeach
            </div>

            @if ($count > 1)
                <div class="cf-controls">
                    <button type="button" id="cf-prev" class="gallery-nav" aria-label="Foto sebelumnya"><span class="material-symbols-outlined">arrow_back</span></button>
                    <div class="cf-info" id="cf-info" aria-live="polite">
                        <p class="cf-room" id="cf-room">{{ $first[0] }}</p>
                        <p class="cf-title" id="cf-title">{{ $first[1] ?? '' }}</p>
                    </div>
                    <button type="button" id="cf-next" class="gallery-nav" aria-label="Foto berikutnya"><span class="material-symbols-outlined">arrow_forward</span></button>
                </div>
                <div class="cf-dots" role="tablist" aria-label="Pilih foto">
                    @foreach ($gallery as $i => $photo)
                        <button type="button" class="cf-dot {{ $i === 0 ? 'is-active' : '' }}" role="tab" aria-label="Foto {{ $i + 1 }}: {{ $photo['caption'] }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}"><i></i></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- FASILITAS --}}
<section class="reveal bg-low/60 px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Kelengkapan Kos</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Fasilitas</h2>
            <p class="mt-1.5 text-muted">Kenyamanan menyeluruh untuk menunjang aktivitas dan istirahat Anda.</p>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach (config('kos.facilities') as [$label, $icon])
                <div class="card card-hover group p-5">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-fixed text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                        <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-navy">{{ $label }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HARGA --}}
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-3xl">
        <div class="mb-8 text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Kelengkapan Kos</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Harga Sewa</h2>
        </div>
        <div class="relative overflow-hidden rounded-3xl border border-line bg-lowest p-8 text-center md:p-14" style="box-shadow:0 20px 40px -12px rgba(15,42,92,.08)">
            <div class="pointer-events-none absolute -top-16 left-1/2 h-32 w-80 -translate-x-1/2 rounded-full bg-cyan/20 blur-2xl"></div>
            <span class="chip chip-ok mb-4 !normal-case"><span class="material-symbols-outlined text-[16px]">schedule</span> Pembayaran setiap 3 bulan sekali</span>
            <div class="my-5">
                <div class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">{{ $priceRange ?? 'Hubungi kami' }}</div>
                <div class="mt-1 text-muted">per bulan</div>
                @if ($periodRange)
                    <div class="mx-auto mt-5 inline-flex flex-col items-center rounded-2xl border border-line bg-low px-6 py-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-muted">Total sewa per {{ $paymentMonths }} bulan</span>
                        <span class="mt-0.5 text-xl font-extrabold tracking-tight text-navy">{{ $periodRange }}</span>
                    </div>
                @endif
            </div>
            <div class="mx-auto max-w-lg border-t border-line pt-5">
                <div class="flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-8">
                    @foreach (['6 Kamar Privat', 'Fasilitas Pokok Termasuk', 'Khusus Putri Aman'] as $point)
                        <span class="flex items-center gap-2 text-[13px] font-medium text-navy">
                            <span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>{{ $point }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-8">
                <a href="{{ route('rooms.index') }}" class="btn btn-primary !px-8">Pilih Kamar Sekarang <span class="material-symbols-outlined text-[18px]">chevron_right</span></a>
            </div>
        </div>
    </div>
</section>

{{-- LOKASI --}}
@php $mapQ = rawurlencode(config('kos.maps_query')); @endphp
<section class="reveal bg-low/60 px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mb-8 text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Temukan Kami</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Lokasi</h2>
        </div>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="card flex flex-col justify-between p-6 lg:col-span-4">
                <div>
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-fixed text-primary"><span class="material-symbols-outlined text-[24px]">location_on</span></span>
                    <h3 class="text-lg font-bold text-navy">{{ config('kos.name') }}</h3>
                    <p class="mt-2 leading-relaxed text-muted">{{ config('kos.address') }}</p>
                </div>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQ }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full"><span class="material-symbols-outlined text-[18px]">near_me</span> Buka di Google Maps</a>
                    <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline w-full">@include('partials.wa-icon') Tanya via WhatsApp</a>
                </div>
            </div>
            <div class="overflow-hidden rounded-2xl border border-line bg-container shadow-md lg:col-span-8">
                <iframe title="Peta lokasi {{ config('kos.name') }}" src="https://www.google.com/maps?q={{ $mapQ }}&output=embed" class="h-[340px] w-full lg:h-full lg:min-h-[380px]" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>

{{-- CARA MEMESAN --}}
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Mudah &amp; Cepat</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Cara Memesan</h2>
        </div>
        {{-- Stepper: ikon + nomor menyatu dalam satu "tile", dihubungkan garis putus-putus --}}
        <div class="grid grid-cols-1 gap-10 md:grid-cols-3 md:gap-8">
            @foreach ([
                ['1', 'meeting_room', 'Pilih Kamar', 'Lihat daftar kamar, foto, harga, dan pilih yang masih tersedia.'],
                ['2', 'edit_note', 'Kirim Formulir / WhatsApp', 'Isi formulir pemesanan di halaman kamar, atau chat langsung via WhatsApp.'],
                ['3', 'task_alt', 'Konfirmasi Pengelola', 'Pengelola menghubungi Anda untuk konfirmasi dan langkah selanjutnya.'],
            ] as [$n, $icon, $title, $text])
                <div class="relative flex flex-col items-center text-center">
                    <div class="relative">
                        <span class="flex h-16 w-16 items-center justify-center rounded-2xl border border-line bg-lowest text-primary shadow-lg shadow-primary/10">
                            <span class="material-symbols-outlined text-[30px]">{{ $icon }}</span>
                        </span>
                        <span class="bg-grad absolute -right-2.5 -top-2.5 flex h-7 w-7 items-center justify-center rounded-full text-xs font-extrabold text-white shadow-md ring-[3px] ring-lowest">{{ $n }}</span>
                    </div>
                    @if (! $loop->last)
                        <span aria-hidden="true" class="pointer-events-none absolute top-8 hidden border-t-2 border-dashed border-primary/30 md:block" style="left:calc(50% + 2.75rem);width:calc(100% - 3.5rem)"></span>
                    @endif
                    <h3 class="mt-5 text-lg font-bold text-navy">{{ $title }}</h3>
                    <p class="mt-1.5 max-w-xs text-sm leading-relaxed text-muted">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TENTANG --}}
<section class="reveal bg-low/40 px-5 py-14 md:px-8">
    <div class="mx-auto grid max-w-page grid-cols-1 items-center gap-8 lg:grid-cols-12">
        <div class="flex flex-col items-start gap-5 lg:col-span-7">
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Tentang {{ config('kos.name') }}</h2>
            <p class="text-lg leading-relaxed text-muted">{{ config('kos.about') }}</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach (['Khusus Perempuan', 'Tenang & Privat', 'Lingkungan Nyaman', 'Bayar 3 Bulan Sekali'] as $tag)
                    <span class="rounded-full border border-line bg-lowest px-4 py-2 text-sm font-semibold text-navy shadow-sm">{{ $tag }}</span>
                @endforeach
            </div>
            <div class="card flex w-full items-start gap-3 p-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-fixed text-primary"><span class="material-symbols-outlined text-[20px]">location_on</span></span>
                <div>
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-primary">Lokasi Kos</span>
                    <p class="leading-relaxed text-muted">{{ config('kos.address') }}</p>
                </div>
            </div>
        </div>
        <div class="lg:col-span-5">
            <div class="rounded-3xl border border-line bg-lowest p-3 shadow-xl">
                <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-container">
                    @if ($heroImage)<img src="{{ $heroImage }}" alt="Kamar nyaman di {{ config('kos.name') }}" class="h-full w-full object-cover" loading="lazy">@endif
                    <div class="absolute inset-x-0 bottom-0 bg-[#0F2A5C]/85 p-5 text-white">
                        <span class="block text-lg font-bold">Ketenangan Maksimal</span>
                        <span class="block text-[13px] text-white/85">Hanya {{ config('kos.total_rooms') }} kamar menjamin suasana yang sunyi dan kondusif untuk belajar maupun istirahat.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONI (tampil hanya jika config('kos.testimonials') terisi) --}}
@if (count(config('kos.testimonials')))
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Kata Mereka</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">Testimoni Penghuni</h2>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            @foreach (config('kos.testimonials') as $t)
                <figure class="card p-6">
                    <span class="material-symbols-outlined mb-2 text-[28px] text-cyan">format_quote</span>
                    <blockquote class="leading-relaxed text-navy">{{ $t['isi'] }}</blockquote>
                    <figcaption class="mt-4 flex items-center gap-3">
                        <span class="bg-grad flex h-10 w-10 items-center justify-center rounded-full text-sm font-extrabold text-white">{{ mb_strtoupper(mb_substr($t['nama'], 0, 1)) }}</span>
                        <span><span class="block text-sm font-bold text-navy">{{ $t['nama'] }}</span><span class="block text-[13px] text-muted">{{ $t['peran'] ?? '' }}</span></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FAQ --}}
@php
    $faqVars = [
        '{harga}' => $priceRange ?? 'sesuai kamar',
        '{pembayaran}' => rtrim(config('kos.payment_period'), '.'),
        '{fasilitas}' => \Illuminate\Support\Str::ucfirst(collect(config('kos.facilities'))->map(fn ($f) => mb_strtolower($f[0]))->implode(', ')),
        '{alamat}' => config('kos.address'),
        '{whatsapp}' => config('kos.whatsapp_display'),
    ];
@endphp
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-3xl">
        <div class="mb-8 text-center">
            <span class="mb-1.5 inline-block text-[11px] font-bold uppercase tracking-wider text-primary">Pertanyaan Umum</span>
            <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px]">FAQ</h2>
        </div>
        <div class="space-y-3">
            @foreach (config('kos.faqs') as $n => [$q, $a])
                <details class="faq card group !rounded-2xl" @if($n === 0) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-5 font-bold text-navy">
                        <span>{{ $q }}</span>
                        <span class="material-symbols-outlined faq-icon shrink-0 text-[22px] text-primary transition-transform">expand_more</span>
                    </summary>
                    <p class="px-5 pb-5 leading-relaxed text-muted">{{ strtr($a, $faqVars) }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="reveal px-5 py-14 md:px-8">
    <div class="mx-auto max-w-page">
        <div class="cta-banner relative overflow-hidden rounded-3xl p-8 text-center md:p-14">
            <div class="pointer-events-none absolute -left-20 -top-20 h-64 w-64 cta-blob rounded-full bg-cyan-200/60 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-20 h-64 w-64 cta-blob rounded-full bg-blue-300/50 blur-2xl"></div>
            <div class="relative z-10 mx-auto flex max-w-2xl flex-col items-center gap-3">
                <h2 class="text-[28px] font-extrabold tracking-tight text-navy md:text-[40px] md:leading-[48px]">Temukan Kamar Impian Anda di {{ config('kos.name') }}</h2>
                <p class="mb-2 max-w-lg text-muted">Kapasitas terbatas hanya {{ config('kos.total_rooms') }} kamar untuk menjaga kenyamanan dan ketenangan setiap penghuni.</p>
                <a href="{{ route('rooms.index') }}" class="btn btn-primary !px-8">Lihat Daftar Kamar <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/home.js') }}" defer></script>
@endpush
