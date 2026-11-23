<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('kos.name') }}</title>
    @include('partials.assets')
    @stack('head')
</head>
<body class="antialiased">
@auth
    <header class="glass fixed inset-x-0 top-0 z-50">
        <div class="mx-auto flex h-nav max-w-page items-center justify-between px-5 md:px-8">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3">
                    <span class="bg-grad flex h-9 w-9 items-center justify-center rounded-xl text-white shadow-sm transition-transform group-hover:scale-105">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-[18px] font-extrabold tracking-tight text-navy">{{ config('kos.name') }}</span>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-primary">Admin</span>
                    </span>
                </a>
                <nav class="admin-nav hidden items-center gap-6 md:flex" aria-label="Menu admin">
                    <a data-nav="dasbor" href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-muted transition-colors hover:text-navy {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dasbor</a>
                    <a data-nav="kamar" href="{{ route('admin.dashboard') }}#data-kamar" class="text-sm font-semibold text-muted transition-colors hover:text-navy {{ request()->routeIs('admin.rooms.*') ? 'is-active' : '' }}">Kamar</a>
                    <a data-nav="pesan" href="{{ route('admin.dashboard') }}#pesan-masuk" class="text-sm font-semibold text-muted transition-colors hover:text-navy">Pesan</a>
                </nav>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                @include('partials.theme-toggle')
                <a href="{{ route('home') }}" target="_blank" class="hidden items-center gap-1.5 rounded-lg border border-line bg-low px-3 py-1.5 text-[12px] font-bold text-muted hover:text-navy sm:inline-flex">
                    <span class="material-symbols-outlined text-[16px] text-primary">open_in_new</span> Lihat Situs
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-high px-3 py-2 text-[13px] font-bold text-navy transition hover:brightness-95">
                        <span class="material-symbols-outlined text-[18px]">logout</span><span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>
@endauth

    <main class="{{ auth()->check() ? 'pt-nav' : '' }} min-h-screen">
        @yield('content')
    </main>

    @include('admin.partials.toast')
    @include('partials.scripts')
    <script src="{{ asset('js/admin.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
