@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="mx-auto max-w-lg">
    <div class="rounded-2xl border border-white/10 bg-white/6 p-8 shadow-2xl shadow-slate-950/30 backdrop-blur sm:p-10">
        <p class="mb-3 text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Verifikasi akun</p>
        <h1 class="text-3xl font-semibold tracking-tight text-white">Cek email Anda</h1>
        <p class="mt-4 text-sm leading-6 text-slate-400">
            Tautan verifikasi telah dikirim ke <strong class="text-slate-200">{{ $user->email }}</strong>. Tautan berlaku selama 60 menit.
        </p>

        @if(session('success'))
            <p class="mt-5 rounded-lg border border-emerald-400/30 bg-emerald-400/10 p-4 text-sm text-emerald-200">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <ul class="mt-5 space-y-2 rounded-lg border border-rose-400/30 bg-rose-400/10 p-4 text-sm text-rose-200">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('verification.resend') }}" class="mt-8">
            @csrf
            <button type="submit" class="w-full rounded-lg border border-cyan-300/40 px-4 py-3 text-sm font-semibold text-cyan-200 transition hover:bg-cyan-300/10">Kirim Ulang Email Verifikasi</button>
        </form>

        <a href="{{ route('login') }}" class="mt-5 block text-center text-sm text-slate-400 hover:text-white">Kembali ke login</a>
    </div>
</div>
@endsection
