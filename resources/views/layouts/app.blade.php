<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('kos.name') . ' — Kos Putri Eksklusif di Malang')</title>
    <meta name="description" content="@yield('description', config('kos.tagline') . '. Nyaman, aman, dan privat untuk mahasiswi dan pekerja muda di Malang.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Pratinjau saat link dibagikan (WhatsApp, Instagram, Facebook, dll) --}}
    @php
        $ogImage = trim($__env->yieldContent('og_image'));
        if ($ogImage !== '' && ! \Illuminate\Support\Str::startsWith($ogImage, ['http://', 'https://'])) {
            $ogImage = url($ogImage);
        }
    @endphp
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('kos.name') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('kos.name') . ' — Kos Putri Eksklusif di Malang')">
    <meta property="og:description" content="@yield('description', config('kos.tagline') . '. Nyaman, aman, dan privat untuk mahasiswi dan pekerja muda di Malang.')">
    @if ($ogImage !== '')
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $ogImage }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    @include('partials.assets')
    @stack('head')
</head>
<body class="antialiased">
    @include('partials.header')

    <main class="pt-nav min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')

    <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" aria-label="Chat WhatsApp"
       class="btn btn-primary fixed bottom-6 right-6 z-50 !h-14 !w-14 !rounded-full !p-0 hover:-translate-y-1">
        @include('partials.wa-icon', ['class' => 'w-7 h-7'])
    </a>

    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
