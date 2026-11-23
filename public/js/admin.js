// Halaman admin: toggle sandi (login), buka/tutup isi pesan (dasbor), auto-hide notifikasi.
(function () {
  var pw = document.getElementById('toggle-pw');
  if (pw) pw.addEventListener('click', function () {
    var i = document.getElementById('password'), ic = document.getElementById('toggle-icon');
    var show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    ic.textContent = show ? 'visibility_off' : 'visibility';
  });

  document.querySelectorAll('.msg').forEach(function (p) {
    p.addEventListener('click', function () { p.classList.toggle('line-clamp-2'); });
  });

  var t = document.getElementById('toast');
  if (t) setTimeout(function () {
    t.style.transition = 'opacity .4s'; t.style.opacity = 0;
    setTimeout(function () { t.remove(); }, 400);
  }, 4000);
})();

// Menu navigasi admin: yang sedang aktif berwarna biru, lainnya abu-abu.
// Di dasbor, menu mengikuti klik DAN posisi scroll (Dasbor -> Kamar -> Pesan).
// Di halaman lain (mis. form kamar) status awal ditentukan server.
(function () {
    var links = document.querySelectorAll('.admin-nav [data-nav]');
    var secKamar = document.getElementById('data-kamar');
    var secPesan = document.getElementById('pesan-masuk');
    if (!links.length || !secKamar || !secPesan) return;

    var OFFSET = 130;       // tinggi header + jarak napas
    var lockUntil = 0;      // abaikan scroll-spy sebentar setelah klik menu

    // Murni: menentukan menu aktif dari posisi section terhadap layar
    function pick(kamarTop, pesanTop, viewportH, atBottom) {
        if (pesanTop <= OFFSET) return 'pesan';
        if (atBottom && pesanTop < viewportH - 150) return 'pesan'; // halaman pendek: Pesan tak bisa sampai ke atas
        if (kamarTop <= OFFSET) return 'kamar';
        return 'dasbor';
    }
    function setActive(key) {
        links.forEach(function (l) { l.classList.toggle('is-active', l.getAttribute('data-nav') === key); });
    }
    function update() {
        var doc = document.documentElement;
        var atBottom = window.innerHeight + window.pageYOffset >= doc.scrollHeight - 4;
        setActive(pick(secKamar.getBoundingClientRect().top, secPesan.getBoundingClientRect().top, window.innerHeight, atBottom));
    }

    links.forEach(function (l) {
        l.addEventListener('click', function (e) {
            var key = l.getAttribute('data-nav');
            setActive(key);
            lockUntil = Date.now() + 900;
            if (key === 'dasbor') {       // sudah di dasbor: cukup gulir ke atas
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (history.replaceState) history.replaceState(null, '', location.pathname);
            }
        });
    });

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (ticking || Date.now() < lockUntil) return;
        ticking = true;
        window.requestAnimationFrame(function () { update(); ticking = false; });
    }, { passive: true });
    window.addEventListener('resize', update);
    window.addEventListener('load', function () { setTimeout(update, 250); }); // setelah browser melompat ke #anchor
    update();
})();

