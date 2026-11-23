@extends('layouts.admin')

@section('title', 'Login Admin')

@section('content')
<div class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-12">
    <div class="orb left-1/2 top-10 h-[360px] w-[620px] -translate-x-1/2"></div>
    <div class="orb -bottom-20 -right-20 h-[300px] w-[400px] opacity-70"></div>

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mb-6 inline-flex items-center gap-3">
                <span class="bg-grad flex h-11 w-11 items-center justify-center rounded-xl text-white shadow-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </span>
                <span class="text-left leading-tight">
                    <span class="block text-xl font-extrabold tracking-tight text-navy">{{ config('kos.name') }}</span>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-primary">Kos Putri</span>
                </span>
            </div>
            <h1 class="text-[36px] font-extrabold tracking-tight text-navy">Login <span class="text-grad">Admin</span></h1>
            <p class="mt-1 text-muted">Khusus pemilik {{ config('kos.name') }}</p>
        </div>

        <div class="card !rounded-3xl p-6 md:p-8" style="box-shadow:0 20px 40px -12px rgba(15,42,92,.1)">
            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5" novalidate>
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-sm font-semibold text-navy">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline">mail</span>
                        <input id="email" name="email" type="email" required autofocus autocomplete="username" value="{{ old('email') }}" placeholder="admin@bluehousekos.com" class="field !pl-11 {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    </div>
                    @error('email') <span class="flex items-center gap-1 text-[13px] font-medium text-[#ba1a1a]"><span class="material-symbols-outlined text-[16px]">info</span>{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-semibold text-navy">Kata Sandi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline">lock</span>
                        <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="field !px-11 {{ $errors->has('password') ? 'is-invalid' : '' }}">
                        <button type="button" id="toggle-pw" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary">
                            <span id="toggle-icon" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    @error('password') <span class="flex items-center gap-1 text-[13px] font-medium text-[#ba1a1a]"><span class="material-symbols-outlined text-[16px]">info</span>{{ $message }}</span> @enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-line accent-[#2563EB]"> Ingat saya di perangkat ini
                </label>

                <button type="submit" class="btn btn-primary w-full">Masuk <span class="material-symbols-outlined text-[18px]">login</span></button>
            </form>
        </div>

        <p class="mt-6 text-center text-[13px] text-outline">
            <a href="{{ route('home') }}" class="font-semibold text-primary hover:underline">← Kembali ke situs</a>
            &nbsp;·&nbsp; © {{ date('Y') }} {{ config('kos.name') }}
        </p>
    </div>
</div>
@endsection
