<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungkan WhatsApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 px-6 py-10 text-slate-100">
    <main class="mx-auto max-w-lg rounded-2xl border border-white/10 bg-white/6 p-8 text-center">
        <p class="text-sm uppercase tracking-[0.2em] text-cyan-300">Fonnte Device</p>
        <h1 class="mt-3 text-2xl font-semibold">Hubungkan {{ $bot->name }}</h1>

        @if($connected)
            <p class="mt-4 text-emerald-300">WhatsApp sudah terhubung.</p>
        @elseif($qr)
            <p class="mt-4 text-slate-400">Buka WhatsApp di ponsel, pilih Perangkat Tertaut, lalu scan QR berikut.</p>
            <img class="mx-auto mt-6 aspect-square w-full max-w-sm rounded-xl bg-white p-4" src="{{ $qr }}" alt="QR WhatsApp Fonnte">
            <p class="mt-4 text-xs text-slate-500">QR dapat kedaluwarsa. Muat ulang halaman untuk mengambil QR baru.</p>
        @else
            <p class="mt-4 text-amber-300">{{ $message ?: 'QR belum tersedia.' }}</p>
        @endif

        <a class="mt-6 inline-flex rounded-lg border border-white/15 px-4 py-2 text-sm text-slate-200 hover:border-cyan-300/60" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    </main>
</body>
</html>
