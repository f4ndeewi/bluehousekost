// Landing page: lightbox foto + carousel galeri coverflow 3D.
(function () {
    var slides = Array.prototype.slice.call(
        document.querySelectorAll("[data-slide]"),
    );
    if (!slides.length) return;

    /* ============ Lightbox (klik foto tengah untuk memperbesar) ============ */
    var box = null,
        idx = 0;

    function closeBox() {
        if (box) {
            box.remove();
            box = null;
            document.removeEventListener("keydown", onKey);
            document.body.style.overflow = "";
        }
    }
    function onKey(e) {
        if (e.key === "Escape") closeBox();
        else if (e.key === "ArrowRight") openBox(idx + 1);
        else if (e.key === "ArrowLeft") openBox(idx - 1);
    }
    function openBox(i) {
        idx = (i + slides.length) % slides.length;
        var it = slides[idx];
        if (!box) {
            box = document.createElement("div");
            box.className = "lightbox";
            box.setAttribute("role", "dialog");
            box.setAttribute("aria-modal", "true");
            box.innerHTML =
                '<button type="button" aria-label="Tutup">&times;</button><img alt=""><p></p>';
            box.addEventListener("click", function (e) {
                if (e.target === box || e.target.tagName === "BUTTON")
                    closeBox();
            });
            document.body.appendChild(box);
            document.body.style.overflow = "hidden";
            document.addEventListener("keydown", onKey);
        }
        var img = box.querySelector("img");
        img.src = it.getAttribute("data-src");
        img.alt = it.getAttribute("data-caption") || "";
        box.querySelector("p").textContent =
            it.getAttribute("data-caption") || "";
    }

    /* ============ Carousel coverflow 3D (gerak berbasis fisika pegas) ============ */
    var stage = document.getElementById("cf-stage");
    if (!stage) return;

    var n = slides.length,
        a = 0; // a = indeks foto aktif (logis)
    var cf = document.getElementById("cf");
    var DUR = parseInt(cf.getAttribute("data-autoplay"), 10) || 5200;
    var reduce =
        window.matchMedia &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var elRoom = document.getElementById("cf-room"),
        elTitle = document.getElementById("cf-title"),
        elInfo = document.getElementById("cf-info");
    var dots = Array.prototype.slice.call(document.querySelectorAll(".cf-dot"));
    var btnPrev = document.getElementById("cf-prev"),
        btnNext = document.getElementById("cf-next");

    var playing = n > 1 && !reduce,
        hovering = false,
        focused = false,
        inView = true,
        timer = null;
    cf.style.setProperty("--cf-dur", DUR + "ms");

    // Lapisan gelap (opacity) untuk foto samping: jauh lebih ringan daripada CSS filter
    slides.forEach(function (s) {
        var dim = document.createElement("span");
        dim.className = "cf-dim";
        dim.setAttribute("aria-hidden", "true");
        s._img = s.querySelector("img"); // dipakai efek parallax
        s.insertBefore(dim, s._img.nextSibling);
    });

    function mod(x, m) {
        return ((x % m) + m) % m;
    }

    // Selisih posisi slide i terhadap indeks aktif (memutar/loop), mis. -2,-1,0,1,2
    function rel(i, active, total) {
        var d = i - active;
        if (total > 1) {
            d = mod(d, total);
            if (d > total / 2) d -= total;
        }
        return d;
    }

    // Parameter per tingkat jarak |e| = 0,1,2,3 (diinterpolasi linear untuk nilai pecahan)
    var SET = {
        wide: {
            x: [0, 0.66, 1.04, 1.3],
            s: [1, 0.82, 0.66, 0.55],
            r: [0, 46, 52, 56],
        },
        narrow: {
            x: [0, 0.86, 1.28, 1.5],
            s: [1, 0.84, 0.7, 0.6],
            r: [0, 32, 38, 42],
        },
    };
    var OPA = [1, 0.92, 0.55, 0],
        DIM = [0, 0.2, 0.38, 0.5];
    function lerp(arr, t) {
        t = Math.min(Math.max(t, 0), 3);
        var i = Math.min(Math.floor(t), 2),
            f = t - i;
        return arr[i] + (arr[i + 1] - arr[i]) * f;
    }
    var metrics = { sw: 600, set: SET.wide };

    // Pudar menjelang titik loop (|e| = n/2) agar foto yang "menyeberang" muncul/hilang mulus, tanpa loncatan terlihat
    function wrapFade(ae) {
        if (n < 3) return 1;
        return Math.min(1, Math.max(0, (n / 2 - ae) / 0.5));
    }

    function place(s, e) {
        var m = metrics,
            ae = Math.abs(e),
            sign = e < 0 ? -1 : 1;
        var x = ae === 0 ? 0 : sign * lerp(m.set.x, ae) * m.sw;
        var rot = ae === 0 ? 0 : -sign * lerp(m.set.r, ae);
        var z = -Math.min(ae, 3) * 110;
        var fade = wrapFade(ae);
        var y = -(1 - Math.min(ae, 1)) * 12; // foto tengah sedikit terangkat
        s.style.transform =
            "translate3d(" +
            x.toFixed(1) +
            "px," +
            y.toFixed(1) +
            "px," +
            z.toFixed(1) +
            "px) rotateY(" +
            rot.toFixed(2) +
            "deg) scale(" +
            lerp(m.set.s, ae).toFixed(4) +
            ")";
        s.style.opacity = String(+(lerp(OPA, ae) * fade).toFixed(3));
        s.style.zIndex = String(100 - Math.round(ae * 10));
        s.querySelector(".cf-dim").style.opacity = String(
            +lerp(DIM, ae).toFixed(3),
        );
        // Parallax: isi foto bergeser lebih lambat daripada bingkainya (kesan kedalaman)
        if (!reduce && s._img)
            s._img.style.transform =
                "translate3d(" +
                (-Math.max(-1.5, Math.min(1.5, e)) * 3.5).toFixed(2) +
                "%,0,0)";
    }

    function measure() {
        var sw = slides[0].offsetWidth;
        metrics.sw = sw;
        metrics.set = stage.clientWidth < 640 ? SET.narrow : SET.wide;
        stage.style.height = Math.round(sw * 0.75 + 56) + "px";
        slides.forEach(function (s) {
            s.style.height = Math.round(sw * 0.75) + "px";
        });
    }

    /* ---- Mesin gerak: transisi CSS (jalan di compositor, tetap mulus walau JS sibuk). JS hanya menentukan posisi tujuan ---- */
    var pos = 0,
        target = 0,
        dragging = false;

    // Pasang posisi semua foto untuk indeks kontinu `pos`. instant=true: tanpa animasi (awal/resize)
    function render(instant) {
        var thr = Math.max(1.6, n / 2);
        slides.forEach(function (s, i) {
            var e = n >= 3 ? i - pos - n * Math.round((i - pos) / n) : i - pos;
            // Foto yang "menyeberang" lewat belakang (loncat jauh) dipindah tanpa animasi; ia sedang transparan
            var snap =
                instant || (s._e !== undefined && Math.abs(e - s._e) > thr);
            if (snap) s.classList.add("cf-snap");
            place(s, e);
            s._e = e;
            if (snap) {
                void s.offsetWidth;
                s.classList.remove("cf-snap");
            }
        });
    }

    // Status statis (kelas aktif, klik, aksesibilitas) mengikuti foto aktif
    function syncStatic() {
        slides.forEach(function (s, i) {
            var d = rel(i, a, n),
                ae = Math.abs(d);
            var hidden = ae >= 3 || wrapFade(ae) === 0;
            s.style.pointerEvents = hidden ? "none" : "auto";
            s.tabIndex = hidden ? -1 : 0;
            s.classList.toggle("is-active", d === 0);
            s.setAttribute("aria-hidden", hidden ? "true" : "false");
        });
    }

    // Keterangan: memudar dulu, lalu berganti saat foto bergerak
    var capTok = 0;
    function setCaption() {
        var parts = (slides[a].getAttribute("data-caption") || "").split(" · ");
        elRoom.textContent = parts[0] || "";
        elTitle.textContent = parts[1] || "";
    }
    function updateInfo(first) {
        dots.forEach(function (d, i) {
            d.classList.toggle("is-active", i === a);
            d.setAttribute("aria-current", i === a ? "true" : "false");
        });
        if (!elRoom) return;
        var t = ++capTok;
        if (first) {
            setCaption();
            return;
        }
        elInfo.classList.remove("cf-swap");
        elInfo.classList.add("cf-out");
        setTimeout(function () {
            if (t !== capTok) return;
            setCaption();
            elInfo.classList.remove("cf-out");
            void elInfo.offsetWidth; // restart animasi masuk
            elInfo.classList.add("cf-swap");
        }, 220);
    }

    // Jadwal autoplay + isi progres di titik aktif; berhenti saat hover/fokus/tab tersembunyi/di luar layar
    function refresh() {
        clearTimeout(timer);
        dots.forEach(function (d) {
            d.classList.remove("is-run");
        });
        if (
            n > 1 &&
            playing &&
            !hovering &&
            !focused &&
            inView &&
            !document.hidden
        ) {
            void cf.offsetWidth; // restart animasi
            dots[a].classList.add("is-run");
            timer = setTimeout(function () {
                go(a + 1);
            }, DUR);
        }
    }

    // Pindah ke foto i lewat jalur terpendek; CSS transition yang menganimasikan
    function go(i) {
        var idx = mod(i, n),
            delta = rel(idx, a, n);
        a = idx;
        target = n >= 3 ? target + delta : idx; // n<3: tanpa loop
        pos = target;
        syncStatic();
        updateInfo(false);
        refresh();
        render(false);
    }

    // Klik: foto samping -> jadikan tengah; foto tengah -> perbesar
    var dragged = false;
    slides.forEach(function (s, i) {
        s.addEventListener("click", function (e) {
            if (dragged) {
                e.preventDefault();
                return;
            }
            if (i === a) openBox(i);
            else go(i);
        });
    });

    if (btnPrev)
        btnPrev.addEventListener("click", function () {
            go(a - 1);
        });
    if (btnNext)
        btnNext.addEventListener("click", function () {
            go(a + 1);
        });
    dots.forEach(function (d, i) {
        d.addEventListener("click", function () {
            go(i);
        });
    });

    /* ---- Drag/swipe: foto ikut pointer; saat dilepas, transisi CSS menyelesaikan gerakannya ---- */
    var sx = 0,
        startPos = 0,
        tracking = false,
        samples = [];
    function stepPx() {
        return metrics.sw * metrics.set.x[1];
    }
    function now() {
        return window.performance && performance.now
            ? performance.now()
            : Date.now();
    }

    stage.addEventListener("pointerdown", function (e) {
        if (e.pointerType === "mouse" && e.button !== 0) return;
        tracking = true;
        dragging = false;
        dragged = false;
        sx = e.clientX;
        startPos = pos;
        samples = [];
    });
    window.addEventListener("pointermove", function (e) {
        if (!tracking) return;
        var dx = e.clientX - sx;
        if (!dragging && Math.abs(dx) > 6 && n > 1) {
            dragging = true;
            stage.classList.add("is-dragging"); // matikan transisi agar foto menempel di pointer
            clearTimeout(timer);
            dots.forEach(function (d) {
                d.classList.remove("is-run");
            });
        }
        if (dragging) {
            var p = startPos - dx / stepPx();
            if (n < 3) p = Math.max(0, Math.min(1, p));
            pos = p;
            samples.push({ t: now(), p: pos });
            if (samples.length > 6) samples.shift();
            render(false);
        }
    });
    function endDrag(e, cancelled) {
        if (!tracking) return;
        tracking = false;
        var dx = cancelled ? 0 : e.clientX - sx;
        var wasDragging = dragging;
        dragging = false;
        stage.classList.remove("is-dragging");
        void stage.offsetWidth; // transisi aktif lagi sebelum posisi tujuan dipasang
        if (wasDragging && !cancelled) dx = -(pos - startPos) * stepPx(); // keputusan berdasarkan posisi foto yang sebenarnya

        // kecepatan dari sampel terakhir (posisi/detik) untuk mendeteksi flick
        var v = 0;
        if (samples.length > 1) {
            var f = samples[0],
                l = samples[samples.length - 1];
            if (l.t - f.t > 8 && now() - l.t < 120)
                v = (l.p - f.p) / ((l.t - f.t) / 1000);
        }
        var base = Math.round(startPos);
        var delta = pos - startPos + Math.max(-4, Math.min(4, v)) * 0.12; // proyeksi momentum
        var dir = 0;
        if (!cancelled && (Math.abs(dx) > 45 || Math.abs(delta) > 0.18))
            dir = dx !== 0 ? (dx < 0 ? 1 : -1) : delta > 0 ? 1 : -1;

        if (dir) {
            dragged = true;
            go(mod(Math.round(startPos), n) + dir);
        } else if (wasDragging) {
            target = n >= 3 ? base : Math.round(startPos);
            pos = target;
            render(false); // geseran kurang jauh: kembali ke posisi semula
            refresh();
        }
        setTimeout(function () {
            dragged = false;
        }, 0);
    }
    window.addEventListener("pointerup", function (e) {
        endDrag(e, false);
    });
    window.addEventListener("pointercancel", function (e) {
        endDrag(e, true);
    });

    // Keyboard
    stage.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") {
            e.preventDefault();
            go(a + 1);
        } else if (e.key === "ArrowLeft") {
            e.preventDefault();
            go(a - 1);
        } else if (e.key === "Home") {
            e.preventDefault();
            go(0);
        } else if (e.key === "End") {
            e.preventDefault();
            go(n - 1);
        }
    });

    // Jeda saat disorot / difokuskan
    cf.addEventListener("mouseenter", function () {
        hovering = true;
        refresh();
    });
    cf.addEventListener("mouseleave", function () {
        hovering = false;
        refresh();
    });
    // Jeda karena fokus hanya untuk fokus keyboard; klik mouse tidak boleh membuat autoplay berhenti selamanya
    cf.addEventListener("focusin", function (e) {
        var kb = true;
        try {
            kb = e.target.matches(":focus-visible");
        } catch (x) {}
        focused = kb;
        refresh();
    });
    cf.addEventListener("focusout", function () {
        focused = false;
        refresh();
    });
    document.addEventListener("visibilitychange", refresh);
    if ("IntersectionObserver" in window) {
        new IntersectionObserver(
            function (en) {
                inView = en[0].isIntersecting;
                refresh();
            },
            { threshold: 0.25 },
        ).observe(cf);
    }

    var rafResize = 0;
    window.addEventListener("resize", function () {
        cancelAnimationFrame(rafResize);
        rafResize = requestAnimationFrame(function () {
            measure();
            render(true);
        });
    });

    // Mulai
    stage.classList.add("is-3d");
    measure();
    render(true);
    syncStatic();
    updateInfo(true);
    refresh();
})();
