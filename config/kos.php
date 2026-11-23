<?php

$wa = env('KOS_WHATSAPP', '6282336970925');
$waGreeting = env('KOS_WHATSAPP_GREETING', 'Halo Blue House Kost, saya mau tanya kamar yang tersedia.');

return [
    'name' => 'Blue House Kost',
    'tagline' => 'Kos khusus perempuan dengan 6 kamar',
    'total_rooms' => 6,

    // Nomor WhatsApp format internasional tanpa "+" (untuk wa.me)
    'whatsapp' => env('KOS_WHATSAPP', '6282336970925'),
    'whatsapp_display' => env('KOS_WHATSAPP_DISPLAY', '082336970925'),

    // Link WhatsApp umum (header, beranda, footer) dengan pesan awal otomatis. Ubah teksnya lewat .env: KOS_WHATSAPP_GREETING
    'whatsapp_url' => 'https://wa.me/' . $wa . '?text=' . rawurlencode($waGreeting),

    // Lama periode pembayaran (bulan); dipakai untuk menghitung total di bagian Harga Sewa
    'payment_months' => 3,

    // Email admin untuk notifikasi pesan baru (kosongkan untuk menonaktifkan)
    'admin_email' => env('KOS_ADMIN_EMAIL'),

    'address' => 'Jl. B. Kopi Perum Pondok Kopi Estate II No.Blok 28, Tulusrejo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141',
    'payment_period' => 'Pembayaran sewa setiap 3 bulan sekali',

    'about' => 'Blue House Kost adalah kos khusus perempuan dengan 6 kamar yang menghadirkan suasana tenang, bersih, dan penuh privasi. Dirancang secara khusus untuk mahasiswi dan pekerja muda yang menginginkan tempat tinggal nyaman layaknya di rumah sendiri, dengan suasana yang akrab dan aman.',

    // key => [label, material icon]
    'facilities' => [
        'wifi' => ['Wifi gratis', 'wifi'],
        'listrik' => ['Listrik', 'bolt'],
        'air' => ['Air', 'water_drop'],
        'kamar_mandi_dalam' => ['Kamar mandi dalam', 'shower'],
        'kipas' => ['Kipas angin', 'mode_fan'],
        'dapur' => ['Dapur bersama dengan kulkas', 'kitchen'],
    ],

    // Kata kunci/alamat untuk peta Google Maps di landing page (default: alamat di atas)
    'maps_query' => env('KOS_MAPS_QUERY', 'Jl. B. Kopi Perum Pondok Kopi Estate II No.Blok 28, Tulusrejo, Lowokwaru, Kota Malang'),

    // FAQ di landing page. Placeholder: {harga} {pembayaran} {fasilitas} {alamat} {whatsapp}
    'faqs' => [
        ['Siapa saja yang bisa tinggal di Blue House Kost?', 'Blue House Kost khusus untuk perempuan, cocok untuk mahasiswi dan pekerja muda yang menginginkan hunian yang tenang, bersih, dan privat.'],
        ['Berapa harga sewanya?', 'Harga sewa {harga} per bulan, tergantung kamar. Harga tiap kamar bisa dilihat di halaman Daftar Kamar.'],
        ['Bagaimana sistem pembayarannya?', '{pembayaran}.'],
        ['Fasilitas apa saja yang tersedia?', '{fasilitas}.'],
        ['Bagaimana cara memesan kamar?', 'Pilih kamar yang tersedia, lalu isi formulir pemesanan di halaman detail kamar atau hubungi kami lewat WhatsApp di {whatsapp}. Pengelola akan menghubungi Anda untuk konfirmasi.'],
        ['Di mana lokasi Blue House Kost?', '{alamat}'],
    ],

    // Testimoni penghuni. SENGAJA KOSONG: isi hanya dengan testimoni asli (izin penghuni).
    // Bagian testimoni di landing page otomatis tampil begitu daftar ini berisi.
    // Format: ['nama' => 'Siti', 'peran' => 'Mahasiswi', 'isi' => 'Kosnya tenang dan bersih.']
    'testimonials' => [],

    // Alamat halaman admin: http://domain/{admin_path}/login. Ganti lewat .env (KOS_ADMIN_PATH)
    // ke sesuatu yang tidak mudah ditebak. Hanya huruf kecil, angka, dan tanda minus.
    'admin_path' => trim(env('KOS_ADMIN_PATH', 'admin'), '/'),
];
