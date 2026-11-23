// Galeri interaktif + status tombol kirim pada halaman detail kamar.
(function () {
    var dataEl = document.getElementById('gallery-data');
    var gallery = dataEl ? JSON.parse(dataEl.textContent) : [];
    var img = document.getElementById('main-img');
    var label = document.getElementById('photo-label');
    var thumbs = document.querySelectorAll('[data-thumb]');
    var ring = ['ring-2', 'ring-primary', 'ring-offset-2', 'ring-offset-surface'];
    var current = 0;
     function show(i) {
        if (!img || i === current || !gallery[i]) return;
        current = i;
        img.classList.add('opacity-40', 'scale-[0.98]');
        setTimeout(function () {
            img.src = gallery[i].src;
            img.alt = gallery[i].caption;
            if (label) label.textContent = 'Foto ' + (i + 1) + ' dari ' + gallery.length + ': ' + gallery[i].caption;
            img.classList.remove('opacity-40', 'scale-[0.98]');
        }, 150);
        thumbs.forEach(function (b, idx) {
            var on = idx === i;
            ring.forEach(function (c) { b.classList.toggle(c, on); });
            b.classList.toggle('opacity-75', !on);
        });
    }
    thumbs.forEach(function (b) {
        var i = parseInt(b.getAttribute('data-thumb'), 10);
        b.addEventListener('click', function () { show(i); });
        b.addEventListener('mouseenter', function () { show(i); });
    });
     // Loading state tombol kirim (form tetap dikirim ke server)
    var form = document.getElementById('booking-form');
    if (form) form.addEventListener('submit', function () {
        var btn = document.getElementById('btn-submit');
        btn.disabled = true;
        document.getElementById('btn-icon').textContent = 'progress_activity';
        document.getElementById('btn-icon').classList.add('animate-spin');
        document.getElementById('btn-text').textContent = 'Mengirim...';
    });
})();
