// Form kamar admin: drag & drop foto, pratinjau, hitung karakter, loading simpan.
(function () {
var MAX_FILES = 10, MAX_BYTES = 5 * 1024 * 1024;
var input = document.getElementById('photo-input');
var drop = document.getElementById('dropzone');
var previews = document.getElementById('previews');
var warn = document.getElementById('photo-warn');
var counter = document.getElementById('photo-count');
var dt = new DataTransfer();
 // Hitung karakter deskripsi
var desc = document.getElementById('description'), cc = document.getElementById('char-count');
function count() { cc.textContent = desc.value.length.toLocaleString('id-ID') + ' karakter'; }
desc.addEventListener('input', count); count();
 // Tarif: izinkan hanya angka
var price = document.getElementById('price');
price.addEventListener('input', function () { price.value = price.value.replace(/\D/g, ''); });
 function keptExisting() {
    return Array.prototype.filter.call(document.querySelectorAll('[data-existing] .delete-box'), function (b) { return !b.checked; }).length;
}
function updateCount() {
    var n = keptExisting() + dt.files.length;
    counter.textContent = n + ' Foto Terunggah';
}
document.querySelectorAll('.delete-box').forEach(function (b) {
    b.addEventListener('change', function () {
        b.closest('[data-existing]').classList.toggle('opacity-40', b.checked);
        updateCount();
    });
    b.closest('[data-existing]').classList.toggle('opacity-40', b.checked);
});
 function render() {
    previews.innerHTML = '';
    Array.prototype.forEach.call(dt.files, function (f, i) {
        var box = document.createElement('div');
        box.className = 'relative aspect-[4/3] overflow-hidden rounded-xl bg-container shadow-sm';
        var img = document.createElement('img');
        img.className = 'h-full w-full object-cover';
        img.src = URL.createObjectURL(f);
        img.onload = function () { URL.revokeObjectURL(img.src); };
        var rm = document.createElement('button');
        rm.type = 'button';
        rm.setAttribute('aria-label', 'Hapus foto');
        rm.className = 'absolute right-1.5 top-1.5 flex h-7 w-7 items-center justify-center rounded-full bg-[#0F2A5C]/80 text-white hover:bg-[#ba1a1a]';
        rm.innerHTML = '<span class="material-symbols-outlined text-[16px]">close</span>';
        rm.addEventListener('click', function () { remove(i); });
        box.appendChild(img); box.appendChild(rm);
        previews.appendChild(box);
    });
    input.files = dt.files;
    updateCount();
}
function remove(i) {
    var n = new DataTransfer();
    Array.prototype.forEach.call(dt.files, function (f, idx) { if (idx !== i) n.items.add(f); });
    dt = n; render();
}
function add(files) {
    var msgs = [];
    Array.prototype.forEach.call(files, function (f) {
        if (!/^image\/(png|jpe?g)$/.test(f.type)) { msgs.push(f.name + ': format harus JPG/PNG'); return; }
        if (f.size > MAX_BYTES) { msgs.push(f.name + ': lebih dari 5MB'); return; }
        if (keptExisting() + dt.files.length >= MAX_FILES) { msgs.push('Maksimal ' + MAX_FILES + ' foto per kamar'); return; }
        dt.items.add(f);
    });
    warn.textContent = msgs.join(' • ');
    warn.classList.toggle('hidden', msgs.length === 0);
    render();
}
 input.addEventListener('change', function () { var picked = Array.prototype.slice.call(input.files); input.value = ''; add(picked); });
['dragenter', 'dragover'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('border-primary', 'bg-low'); }); });
['dragleave', 'drop'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('border-primary', 'bg-low'); }); });
drop.addEventListener('drop', function (e) { add(e.dataTransfer.files); });
 // Loading state saat simpan
document.getElementById('room-form').addEventListener('submit', function () {
    var b = document.getElementById('btn-save');
    b.disabled = true;
    var ic = document.getElementById('save-icon');
    ic.textContent = 'progress_activity'; ic.classList.add('animate-spin');
    document.getElementById('save-text').textContent = 'Menyimpan...';
});
 updateCount();
})();
