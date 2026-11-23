@php
    $links = [
        ['Beranda', route('home'), request()->routeIs('home')],
        ['Daftar Kamar', route('rooms.index'), request()->routeIs('rooms.*')],
    ];
@endphp
<header class="glass fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex h-nav max-w-page items-center justify-between px-5 md:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-3">
            <span class="bg-grad flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white shadow-sm transition-transform duration-200 group-hover:scale-105">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </span>
            <span class="text-[20px] font-extrabold leading-tight tracking-tight text-navy">{{ config('kos.name') }}<span class="text-cyan">.</span></span>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            @foreach ($links as [$label, $url, $active])
                <a href="{{ $url }}" @if($active) aria-current="page" @endif
                   class="py-1 text-sm font-semibold transition-colors {{ $active ? 'text-primary font-bold' : 'text-muted hover:text-navy' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            @include('partials.theme-toggle')
            <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary hidden !px-4 !py-2.5 sm:inline-flex">
                @include('partials.wa-icon')
                <span>Chat WhatsApp</span>
            </a>
            @auth
                {{-- Hanya tampil untuk admin yang sedang login; pengunjung tidak melihat apa pun. --}}
                <a href="{{ route('admin.dashboard') }}" class="hidden items-center gap-1.5 rounded-lg border border-line bg-low px-3 py-1.5 text-[11px] font-bold text-muted shadow-sm transition-colors hover:text-navy lg:inline-flex">
                    <span class="material-symbols-outlined text-[16px] text-primary">admin_panel_settings</span> Dasbor Admin
                </a>
            @endauth
            <button id="menu-btn" type="button" aria-label="Menu" class="flex h-9 w-9 items-center justify-center rounded-lg border border-line text-navy md:hidden">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden border-t border-line bg-lowest md:hidden">
        <div class="mx-auto flex max-w-page flex-col gap-1 px-5 py-3">
            @foreach ($links as [$label, $url, $active])
                <a href="{{ $url }}" class="rounded-xl px-3 py-3 text-sm font-semibold {{ $active ? 'bg-primary-fixed text-primary' : 'text-navy' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-2">
                @include('partials.wa-icon') Chat WhatsApp
            </a>
        </div>
    </div>
</header>
