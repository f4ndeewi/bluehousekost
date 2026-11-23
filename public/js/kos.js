// Tema terang/gelap, menu mobile, dan animasi reveal.
// Tema terang/gelap
(function () {
    var root = document.documentElement;
    function sync() {
        var dark = root.classList.contains('dark');
        document.querySelectorAll('[data-theme-icon]').forEach(function (el) { el.textContent = dark ? 'light_mode' : 'dark_mode'; });
            document.querySelectorAll('[data-theme-toggle]').forEach(function (b) { b.setAttribute('aria-checked', dark ? 'true' : 'false'); });
    }
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            root.classList.toggle('dark');
            try { localStorage.setItem('bhk-theme', root.classList.contains('dark') ? 'dark' : 'light'); } catch (e) {}
            sync();
        });
    });
    sync();
     // Menu mobile
    var mb = document.getElementById('menu-btn'), mm = document.getElementById('mobile-menu');
    if (mb && mm) mb.addEventListener('click', function () { mm.classList.toggle('hidden'); });
     // Reveal on scroll
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('is-in'); }); return; }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (e) { io.observe(e); });
})();

// Placeholder otomatis bila foto gagal dimuat (URL kedaluwarsa, file hilang, dsb.)
(function () {
    var PLACEHOLDER = 'data:image/svg+xml;utf8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect width="400" height="300" fill="#e8eff7"/>' +
        '<g fill="none" stroke="#94a3b8" stroke-width="6" stroke-linecap="round" stroke-linejoin="round">' +
        '<rect x="150" y="105" width="100" height="75" rx="10"/><circle cx="178" cy="132" r="7"/><path d="M158 172l32-26 22 18 14-11 16 19"/></g>' +
        '<text x="200" y="215" text-anchor="middle" font-family="sans-serif" font-size="16" fill="#94a3b8">Foto tidak tersedia</text></svg>');

    function fallback(img) {
        if (img.dataset.fallbackDone) return;
        img.dataset.fallbackDone = '1';
        img.src = PLACEHOLDER;
    }
    // error pada <img> tidak "bubble", jadi pakai fase capture
    document.addEventListener('error', function (e) {
        if (e.target && e.target.tagName === 'IMG') fallback(e.target);
    }, true);
    // gambar yang sudah gagal sebelum skrip ini jalan
    document.querySelectorAll('img').forEach(function (img) {
        if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) fallback(img);
    });
    window.BHK_PLACEHOLDER = PLACEHOLDER;
})();
