@php
    $invalid = fn ($f) => $errors->has($f) ? 'is-invalid' : '';
    $checked = $errors->any() ? (array) old('facilities', []) : (array) ($room->facilities ?? []);
    $existing = $mode === 'edit' ? $room->photos : collect();
    $photoErrors = collect($errors->get('photos.*'))->flatten()->push($errors->first('photos'))->filter()->unique();
@endphp

<div class="relative mx-auto max-w-4xl px-5 py-10 md:px-8">
    <div class="orb left-1/4 top-0 h-80 w-96 opacity-70"></div>

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="mb-3 inline-flex items-center gap-1 text-sm font-semibold text-muted hover:text-primary">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Dasbor
            </a>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-[32px] font-extrabold tracking-tight text-navy md:text-[40px]">{{ $heading }}</h1>
                <span class="chip chip-ok"><span class="h-1.5 w-1.5 rounded-full bg-cyan"></span> {{ $badge }}</span>
            </div>
            <p class="mt-1 text-muted">{{ $sub }}</p>
        </div>
        <div class="flex items-center gap-2 rounded-xl border border-line bg-lowest px-4 py-2 text-sm font-bold text-navy shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-primary">add_circle</span> {{ $unit }}
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-[#ba1a1a]/30 bg-[#ffdad6] p-4 text-[#93000a]">
            <span class="material-symbols-outlined">error</span>
            <div class="text-sm">
                <p class="font-bold">Periksa kembali isian Anda:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach (collect($errors->all())->unique() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" id="room-form" novalidate>
        @csrf
        @if ($mode === 'edit') @method('PUT') @endif

        {{-- Informasi Dasar --}}
        <section class="card p-5 md:p-8">
            <div class="mb-5 flex items-center gap-3"><span class="h-6 w-1.5 rounded-full bg-grad"></span><h2 class="text-lg font-bold text-navy">Informasi Dasar</h2></div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="flex justify-between text-sm font-semibold text-navy"><span>Nama Kamar</span><span class="text-[12px] font-normal text-primary">*Wajib</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline">meeting_room</span>
                        <input id="name" name="name" type="text" required maxlength="60" value="{{ old('name', $room->name) }}" placeholder="Contoh: Kamar 7" class="field !pl-11 {{ $invalid('name') }}">
                    </div>
                    <span class="text-[13px] text-muted">Penomoran unit kamar</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="price" class="flex justify-between text-sm font-semibold text-navy"><span>Tarif Sewa</span><span class="text-[12px] font-normal text-primary">*Wajib</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-outline">Rp</span>
                        <input id="price" name="price" type="text" inputmode="numeric" required value="{{ old('price', $room->price) }}" placeholder="Contoh: 800000" class="field !pl-11 !pr-14 {{ $invalid('price') }}">
                        <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[13px] text-outline">/bln</span>
                    </div>
                    <span class="flex items-center gap-1 text-[13px] text-muted"><span class="material-symbols-outlined text-[15px]">info</span> Dibayarkan per 3 bulan</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="status" class="text-sm font-semibold text-navy">Status Unit</label>
                    <select id="status" name="status" class="field cursor-pointer {{ $invalid('status') }}">
                        @foreach (['tersedia' => 'Tersedia', 'terisi' => 'Terisi'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('status', $room->status) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                    <span class="text-[13px] text-muted">Kesiapan dihuni</span>
                </div>
            </div>
        </section>

        {{-- Deskripsi --}}
        <section class="card p-5 md:p-8">
            <div class="mb-5 flex items-center gap-3"><span class="h-6 w-1.5 rounded-full bg-grad"></span><h2 class="text-lg font-bold text-navy">Deskripsi Kamar</h2></div>
            <textarea id="description" name="description" rows="4" maxlength="2000" placeholder="Deskripsikan fasilitas dan kondisi kamar..." class="field resize-y {{ $invalid('description') }}">{{ old('description', $room->description) }}</textarea>
            <div class="mt-1.5 flex justify-between text-[13px] text-muted">
                <span>Sertakan keunggulan pencahayaan alami, jendela luar, dan posisi kamar.</span>
                <span id="char-count">0 karakter</span>
            </div>
        </section>

        {{-- Foto --}}
        <section class="card p-5 md:p-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-3"><span class="h-6 w-1.5 rounded-full bg-grad"></span><h2 class="text-lg font-bold text-navy">Foto Galeri Kamar</h2></div>
                <span id="photo-count" class="rounded-full bg-primary-fixed px-3 py-1 text-[12px] font-bold text-primary">0 Foto Terunggah</span>
            </div>

            @if ($existing->isNotEmpty())
                <p class="mb-3 text-sm text-muted">Foto saat ini. Pilih sampul (foto utama), ubah keterangan, atau tandai untuk dihapus.</p>
                <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3">
                    @foreach ($existing as $photo)
                        <div class="existing-photo overflow-hidden rounded-xl border border-line bg-low transition" data-existing>
                            <div class="relative aspect-[4/3] bg-container">
                                <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" class="h-full w-full object-cover" loading="lazy">
                                <label class="absolute left-2 top-2 flex cursor-pointer items-center gap-1 rounded-full bg-[#0F2A5C]/80 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur-sm">
                                    <input type="radio" name="cover_photo" value="{{ $photo->id }}" class="accent-cyan-400" @checked((int) old('cover_photo', $existing->first()->id) === $photo->id)> Sampul
                                </label>
                            </div>
                            <div class="space-y-2 p-2.5">
                                <input type="text" name="captions[{{ $photo->id }}]" maxlength="60" value="{{ old('captions.' . $photo->id, $photo->caption) }}" placeholder="Keterangan foto" class="field !rounded-lg !px-3 !py-1.5 !text-[13px]">
                                <label class="flex cursor-pointer items-center gap-2 text-[12px] font-semibold text-[#93000a]">
                                    <input type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" class="delete-box h-4 w-4 accent-[#ba1a1a]" @checked(in_array($photo->id, (array) old('delete_photos', [])))> Hapus foto ini
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <label for="photo-input" id="dropzone" class="flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-primary/30 bg-low/60 px-5 py-10 text-center transition hover:border-primary hover:bg-low">
                <input id="photo-input" name="photos[]" type="file" multiple accept="image/png,image/jpeg" class="sr-only">
                <span class="bg-grad mb-3 flex h-14 w-14 items-center justify-center rounded-full text-white shadow-md"><span class="material-symbols-outlined text-[28px]">cloud_upload</span></span>
                <p class="text-navy"><span class="font-bold text-primary">Tarik &amp; letakkan foto di sini</span>, atau klik untuk memilih file</p>
                <p class="mt-1 text-[13px] text-muted">Maksimum 5MB per file • Format foto yang didukung: JPG, PNG (Resolusi ideal 1200 x 900 px)</p>
            </label>
            <p id="photo-warn" class="mt-2 hidden text-[13px] font-semibold text-[#ba1a1a]"></p>
            @foreach ($photoErrors as $e)<p class="mt-2 text-[13px] font-semibold text-[#ba1a1a]">{{ $e }}</p>@endforeach
            <div id="previews" class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4"></div>
        </section>

        {{-- Fasilitas --}}
        <section class="card p-5 md:p-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-3"><span class="h-6 w-1.5 rounded-full bg-grad"></span><h2 class="text-lg font-bold text-navy">Fasilitas Kamar &amp; Hunian</h2></div>
                <span class="text-[13px] text-muted">Semua fasilitas gratis &amp; termasuk tarif</span>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (config('kos.facilities') as $key => [$label, $icon])
                    <label class="relative cursor-pointer">
                        <input type="checkbox" name="facilities[]" value="{{ $key }}" class="peer sr-only" @checked(in_array($key, $checked))>
                        <div class="flex items-center gap-3 rounded-xl border-2 border-line bg-lowest p-3 transition peer-checked:border-primary peer-checked:bg-primary-fixed/50 peer-focus-visible:ring-2 peer-focus-visible:ring-primary">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-fixed text-primary"><span class="material-symbols-outlined text-[20px]">{{ $icon }}</span></span>
                            <span class="flex-1 text-sm font-semibold text-navy">{{ $label }}</span>
                            <span class="tick material-symbols-outlined text-[20px] text-primary opacity-0 transition" aria-hidden="true">check_circle</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </section>

        {{-- Aksi --}}
        <div class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-line bg-lowest p-5 shadow-md sm:flex-row sm:items-center">
            <span class="flex items-center gap-2 text-sm font-semibold text-muted"><span class="material-symbols-outlined text-[20px] text-primary">verified_user</span> Perubahan data langsung disinkronkan ke halaman penyewa</span>
            <div class="flex w-full gap-3 sm:w-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn flex-1 bg-high text-navy hover:brightness-95 sm:flex-none">Batal</a>
                <button type="submit" id="btn-save" class="btn btn-primary flex-1 disabled:pointer-events-none disabled:opacity-80 sm:flex-none">
                    <span class="material-symbols-outlined text-[20px]" id="save-icon">add_circle</span><span id="save-text">{{ $submit }}</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="{{ asset('js/admin-room-form.js') }}" defer></script>
@endpush
