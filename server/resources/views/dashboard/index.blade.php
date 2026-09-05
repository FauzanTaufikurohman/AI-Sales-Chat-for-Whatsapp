@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-10">
    <p class="mb-3 text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Workspace</p>
    <h1 class="text-4xl font-semibold tracking-tight text-white">Dashboard</h1>
    <p class="mt-3 max-w-2xl text-slate-400">Pantau chatbot WhatsApp dan kelola percakapan customer dari satu tempat.</p>
</div>

@if($bot)

    <section class="mb-10 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">

        <div class="rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-6">
        <h2 class="text-lg font-semibold text-white">WhatsApp Chatbot</h2>

        <div class="mt-6 space-y-4 text-sm">
            <p class="flex flex-col gap-1 text-slate-400">Nama <span class="text-base font-medium text-white">{{ $bot->name }}</span></p>
            <p class="flex flex-col gap-1 text-slate-400">Nomor <span class="text-base font-medium text-white">{{ $bot->phone_number }}</span></p>
            <p class="flex items-center gap-2 text-slate-400">Status
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $bot->is_active ? 'bg-emerald-400/15 text-emerald-300' : 'bg-slate-400/15 text-slate-300' }}">
                    {{ $bot->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </p>
        </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-6">
            <p class="text-sm text-slate-400">Status operasional</p>
            <p class="mt-4 text-3xl font-semibold text-white">{{ $bot->is_active ? 'Siap melayani' : 'Perlu perhatian' }}</p>
            <p class="mt-2 text-sm leading-6 text-slate-400">Percakapan terbaru akan muncul di daftar customer.</p>
        </div>

    </section>

    <section>

        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Percakapan Customer</h2>
                <p class="mt-1 text-sm text-slate-400">Ringkasan interaksi terbaru dari customer.</p>
            </div>
            <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-400">{{ $conversations->count() }} percakapan</span>
        </div>

        @forelse($conversations as $conversation)

            <article class="rounded-2xl border border-white/10 bg-white/[0.06] p-6 transition hover:border-cyan-300/40 hover:bg-white/[0.08]">

                <h3 class="text-lg font-semibold text-white">
                    {{ $conversation->customer_name
                        ?: $conversation->customer_phone }}
                </h3>

                <p class="mt-1 text-sm text-slate-400">
                    {{ $conversation->customer_phone }}
                </p>

                @if($conversation->summary)

                    <p class="mt-5 text-sm leading-6 text-slate-300">
                        <strong class="text-slate-100">Summary:</strong>

                        {{ $conversation->summary->summary }}
                    </p>

                    <p class="mt-2 text-sm text-slate-300">
                        <strong class="text-slate-100">Intent:</strong>

                        {{ $conversation->summary->intent }}
                    </p>

                @endif

                <a class="mt-6 inline-flex rounded-lg bg-cyan-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300" href="{{ route(
                    'dashboard.conversation',
                    $conversation
                ) }}">
                    Lihat Percakapan
                </a>

            </article>

        @empty

            <p class="rounded-2xl border border-dashed border-white/15 bg-white/[0.03] p-8 text-center text-slate-400">
                Belum ada percakapan customer.
            </p>

        @endforelse

    </section>

@else

    <p>
        Chatbot belum tersedia.
    </p>

@endif

@endsection