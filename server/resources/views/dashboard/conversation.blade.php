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

@foreach($conversation->messages as $message)

    <div class="rounded-2xl border border-white/10 bg-white/6 p-5 {{ $message->sender_type === 'customer' ? 'mr-0 sm:mr-16' : 'ml-0 sm:ml-16' }}">

        <strong class="text-sm font-semibold {{ $message->sender_type === 'customer' ? 'text-cyan-300' : 'text-amber-300' }}">
            @if($message->sender_type === 'customer')
                Customer
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