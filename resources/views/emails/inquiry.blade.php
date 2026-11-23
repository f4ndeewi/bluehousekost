<!DOCTYPE html>
<html lang="id">
<body style="font-family:Arial,sans-serif;color:#0F2A5C;line-height:1.6">
    <h2 style="margin:0 0 8px">{{ ucfirst($inquiry->type) }} baru — {{ $inquiry->room?->name ?? 'Umum' }}</h2>
    <table cellpadding="6" style="border-collapse:collapse">
        <tr><td><strong>Nama</strong></td><td>{{ $inquiry->name }}</td></tr>
        <tr><td><strong>WhatsApp</strong></td><td><a href="https://wa.me/{{ preg_replace('/^(\+62|0)/', '62', $inquiry->whatsapp) }}">{{ $inquiry->whatsapp }}</a></td></tr>
        <tr><td><strong>Email</strong></td><td>{{ $inquiry->email ?: '-' }}</td></tr>
        <tr><td><strong>Jenis</strong></td><td>{{ ucfirst($inquiry->type) }}</td></tr>
        <tr><td valign="top"><strong>Pesan</strong></td><td>{!! nl2br(e($inquiry->message ?: '-')) !!}</td></tr>
        <tr><td><strong>Waktu</strong></td><td>{{ $inquiry->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</td></tr>
    </table>
</body>
</html>
