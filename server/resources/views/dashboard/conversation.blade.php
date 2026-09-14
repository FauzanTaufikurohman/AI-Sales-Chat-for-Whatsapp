@extends('layouts.app')

@section('title', 'Conversation')

@section('content')

<a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-cyan-300 transition hover:text-cyan-200">
    ← Kembali
</a>

<div class="mt-8">
<h1 class="text-4xl font-semibold tracking-tight text-white">
    {{ $conversation->customer_name
        ?: $conversation->customer_phone }}
</h1>

<p class="mt-2 text-slate-400">
    {{ $conversation->customer_phone }}
</p>

<div class="my-8 h-px bg-white/10"></div>

@if($conversation->summary)

    <section class="rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-6">

        <h2 class="text-xl font-semibold text-white">AI Summary</h2>

        <p class="mt-4 leading-7 text-slate-200">
            {{ $conversation->summary->summary }}
        </p>

        @if($conversation->summary->intent)

            <p class="mt-4 text-sm text-slate-300">
                <strong class="text-slate-100">Intent:</strong>
                {{ $conversation->summary->intent }}
            </p>

        @endif

        <p class="mt-3 text-sm text-slate-300">
            <strong class="text-slate-100">Purchase Intent:</strong>

            {{ $conversation->summary->purchase_intent
                ? 'Ya'
                : 'Tidak' }}
        </p>

    </section>

    <div class="my-8 h-px bg-white/10"></div>

@endif


<h2 class="mb-5 text-2xl font-semibold text-white">Messages</h2>

@if($errors->has('reply'))
    <p class="mb-5 rounded-xl border border-red-400/30 bg-red-400/10 p-4 text-sm text-red-200">
        {{ $errors->first('reply') }}
    </p>
@endif

<form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.conversation.reply', $conversation) }}" class="mb-8 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 p-6">
    @csrf
    <label for="message" class="text-sm font-semibold text-white">Balas customer</label>
    <textarea id="message" name="message" rows="4" maxlength="4096" class="mt-3 w-full rounded-xl border border-white/10 bg-slate-950/60 p-4 text-slate-100 outline-none focus:border-emerald-300/60">{{ old('message') }}</textarea>
    <label for="image" class="mt-4 block text-sm font-semibold text-white">Gambar dari galeri</label>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-3 block w-full text-sm text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-400 file:px-4 file:py-2 file:font-semibold file:text-slate-950">
    <p class="mt-2 text-xs text-slate-400">Opsional. Format JPG, PNG, atau WebP, maksimal 10 MB. Caption dapat dikosongkan jika hanya mengirim gambar.</p>
    <button type="submit" class="mt-4 rounded-lg bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-300">Kirim Balasan</button>
</form>

@foreach($conversation->messages as $message)

    <div class="rounded-2xl border border-white/10 bg-white/6 p-5 {{ $message->sender_type === 'customer' ? 'mr-0 sm:mr-16' : 'ml-0 sm:ml-16' }}">

        <strong class="text-sm font-semibold {{ $message->sender_type === 'customer' ? 'text-cyan-300' : 'text-amber-300' }}">
            @if($message->sender_type === 'customer')
                Customer
            @elseif($message->sender_type === 'human')
                Anda
            @else
                AI Bot
            @endif
        </strong>

        <p class="mt-3 leading-7 text-slate-200">
            {{ $message->message }}
        </p>

        <small class="mt-4 block text-xs text-slate-500">
            {{ $message->sent_at?->format('d M Y H:i') }}
        </small>

    </div>

@endforeach

</div>
@endsection