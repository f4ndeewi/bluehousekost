<footer class="bg-grad-deep text-white">
    <div class="mx-auto max-w-page px-5 py-14 md:px-8">
        <div class="flex flex-col justify-between gap-8 border-b border-white/15 pb-8 md:flex-row md:items-center">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <span class="bg-grad flex h-9 w-9 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </span>
                    <h2 class="text-2xl font-extrabold tracking-tight">{{ config('kos.name') }}</h2>
                </div>
                <p class="text-white/80">{{ config('kos.tagline') }}</p>
                <p class="mt-2 flex max-w-md items-start gap-1.5 text-[13px] text-white/70">
                    <span class="material-symbols-outlined shrink-0 text-[18px] text-cyan-300">location_on</span>
                    <span>{{ config('kos.address') }}</span>
                </p>
            </div>
            <a href="{{ config('kos.whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 transition-opacity hover:opacity-90">
                <span class="bg-grad flex h-9 w-9 items-center justify-center rounded-xl">@include('partials.wa-icon')</span>
                <span>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-white/60">WhatsApp / Kontak</span>
                    <span class="text-lg font-semibold">{{ config('kos.whatsapp_display') }}</span>
                </span>
            </a>
        </div>
        <p class="pt-5 text-center text-[13px] text-white/50 md:text-left">© {{ date('Y') }} {{ config('kos.name') }}. Hak cipta dilindungi.</p>
    </div>
</footer>
