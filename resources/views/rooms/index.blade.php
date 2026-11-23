@extends('layouts.app')

@section('title', 'Daftar Kamar — ' . config('kos.name'))
@section('description', 'Pilihan kamar nyaman, tenang, dan bersih di ' . config('kos.name') . '. Lihat harga, fasilitas, dan ketersediaan setiap kamar.')

@section('content')
<div class="relative overflow-hidden">
    <div class="orb left-1/2 top-12 h-[360px] w-[720px] -translate-x-1/2"></div>
    <div class="orb -right-48 top-[480px] h-[420px] w-[420px] opacity-60"></div>

    <div class="relative mx-auto max-w-page px-5 py-14 md:px-8">
        <header class="mx-auto mb-14 max-w-3xl text-center">
            <h1 class="mb-2 text-[36px] font-extrabold leading-[44px] tracking-tight text-navy md:text-[56px] md:leading-[64px]">Daftar <span class="text-grad">Kamar</span></h1>
            <p class="mx-auto max-w-2xl text-lg text-muted">Pilihan kamar nyaman, tenang, dan bersih untuk kenyamanan istirahat dan belajar Anda.</p>
            <p class="mt-4 text-sm font-semibold text-primary">Tersisa {{ $available }} dari {{ $rooms->count() }} kamar</p>
        </header>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($rooms as $room)
                @php $open = $room->isAvailable(); @endphp
                <article class="card card-hover reveal group flex flex-col overflow-hidden {{ $open ? '' : 'opacity-90' }}">
                    <a href="{{ route('rooms.show', $room) }}" class="relative block aspect-[4/3] w-full overflow-hidden bg-container {{ $open ? '' : 'grayscale-[25%]' }}" aria-label="Lihat detail {{ $room->name }}">
                        @if ($room->cover_url)
                            <img src="{{ $room->cover_url }}" alt="{{ $room->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        @endif
                        <span class="chip {{ $open ? 'chip-ok' : 'chip-full' }} absolute left-4 top-4 !bg-lowest/90 backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full {{ $open ? 'animate-pulse bg-cyan' : 'bg-slate-400' }}"></span>
                            {{ $open ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </a>
                    <div class="flex flex-grow flex-col justify-between p-5">
                        <div>
                            <h2 class="mb-1 text-[22px] font-bold tracking-tight {{ $open ? 'text-navy' : 'text-muted' }}"><a href="{{ route('rooms.show', $room) }}" class="hover:text-primary">{{ $room->name }}</a></h2>
                            <div class="mb-4 flex items-baseline gap-1">
                                <span class="text-[22px] font-extrabold tracking-tight {{ $open ? 'text-navy' : 'text-muted' }}">{{ $room->price_formatted }}</span>
                                <span class="text-[13px] font-medium text-muted">/ bulan</span>
                            </div>
                            {{-- Ringkasan fasilitas (maks. 3) agar kamar bisa dibandingkan tanpa membuka detail --}}
                            @php $facilities = $room->facilityList(); @endphp
                            @if (count($facilities))
                                <ul class="mb-5 flex flex-wrap gap-2">
                                    @foreach (array_slice($facilities, 0, 3) as [$fLabel, $fIcon])
                                        <li class="inline-flex max-w-full items-center gap-1 rounded-full bg-low px-2.5 py-1 text-[12px] font-semibold text-navy">
                                            <span class="material-symbols-outlined text-[15px] text-primary">{{ $fIcon }}</span>
                                            <span class="truncate">{{ $fLabel }}</span>
                                        </li>
                                    @endforeach
                                    @if (count($facilities) > 3)
                                        <li class="inline-flex items-center rounded-full px-2 py-1 text-[12px] font-semibold text-muted">+{{ count($facilities) - 3 }} lagi</li>
                                    @endif
                                </ul>
                            @else
                                <div class="mb-5"></div>
                            @endif
                        </div>
                        @if ($open)
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-primary w-full">Lihat Detail <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                        @else
                            {{-- Kamar terisi: tetap menjaring calon penghuni lewat WhatsApp --}}
                            <a href="https://wa.me/{{ config('kos.whatsapp') }}?text={{ rawurlencode('Halo ' . config('kos.name') . ', saya mau tanya kapan ' . $room->name . ' akan kosong.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline w-full">
                                @include('partials.wa-icon') Tanya Jika Kosong
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="col-span-full text-center text-muted">Belum ada data kamar.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
