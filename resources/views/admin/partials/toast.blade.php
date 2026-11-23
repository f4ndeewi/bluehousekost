@if (session('success'))
    <div id="toast" role="status" class="pop fixed right-5 top-24 z-[60] flex max-w-sm items-center gap-3 rounded-2xl border border-line bg-lowest p-4 shadow-xl">
        <span class="bg-grad flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white"><span class="material-symbols-outlined text-[20px]">done</span></span>
        <p class="text-sm font-semibold text-navy">{{ session('success') }}</p>
    </div>
@endif
