@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')

<div class="mx-auto max-w-md">
<div class="rounded-2xl border border-white/10 bg-white/6 p-8 shadow-2xl shadow-slate-950/30 backdrop-blur sm:p-10">
<p class="mb-3 text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Keamanan akun</p>
<h1 class="text-3xl font-semibold tracking-tight text-white">Verifikasi OTP</h1>

<p class="mt-3 text-sm leading-6 text-slate-400">
    Kode OTP telah dikirim ke:
</p>

<strong class="mt-1 block text-lg text-white">
    {{ $user->phone_number }}
</strong>

@if($errors->any())
    <ul class="mt-5 space-y-2 rounded-lg border border-rose-400/30 bg-rose-400/10 p-4 text-sm text-rose-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('otp.verify') }}" class="mt-8 space-y-6">

    @csrf

    <div>
        <label for="otp" class="mb-2 block text-sm font-medium text-slate-200">Kode OTP</label>

        <input
            type="text"
            name="otp"
            maxlength="6"
            inputmode="numeric"
            autocomplete="one-time-code"
            required
            class="w-full rounded-lg border border-white/15 bg-slate-950/50 px-4 py-3 text-center text-xl tracking-[0.5em] text-white outline-none transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/20"
        >
    </div>

    <button type="submit" class="w-full rounded-lg bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:ring-offset-2 focus:ring-offset-slate-900">
        Verifikasi
    </button>

</form>

</div>
</div>
@endsection