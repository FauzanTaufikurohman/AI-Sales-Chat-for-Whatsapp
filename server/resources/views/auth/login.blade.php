@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="mx-auto max-w-md">
<div class="rounded-2xl border border-white/10 bg-white/6 p-8 shadow-2xl shadow-slate-950/30 backdrop-blur sm:p-10">
<p class="mb-3 text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">AI WhatsApp</p>
<h1 class="text-3xl font-semibold tracking-tight text-white">Login</h1>

<p class="mt-3 text-sm leading-6 text-slate-400">
    Masukkan email atau nomor WhatsApp Anda untuk masuk.
</p>

@if($errors->any())
    <ul class="mt-5 space-y-2 rounded-lg border border-rose-400/30 bg-rose-400/10 p-4 text-sm text-rose-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-6">

    @csrf

    <div>
        <label for="login" class="mb-2 block text-sm font-medium text-slate-200">Email / Nomor WhatsApp</label>

        <input
            type="text"
            name="login"
            value="{{ old('login') }}"
            required
            class="w-full rounded-lg border border-white/15 bg-slate-950/50 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/20"
        >
    </div>

    <div>
        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>

        <input
            type="password"
            name="password"
            required
            class="w-full rounded-lg border border-white/15 bg-slate-950/50 px-4 py-3 text-white outline-none transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/20"
        >
    </div>

    <button type="submit" class="w-full rounded-lg bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:ring-offset-2 focus:ring-offset-slate-900">
        Login
    </button>

</form>

<p class="mt-8 text-center text-sm text-slate-400">
    Belum memiliki akun?

    <a href="{{ route('register') }}" class="font-medium text-cyan-300 transition hover:text-cyan-200">
        Register
    </a>
</p>

</p>
</div>
</div>
@endsection