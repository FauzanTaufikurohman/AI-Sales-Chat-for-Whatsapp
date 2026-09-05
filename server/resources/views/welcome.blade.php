<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AI WhatsApp</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-slate-950 text-slate-100 antialiased">
        <div class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_80%_10%,rgba(34,211,238,0.2),transparent_30%),radial-gradient(circle_at_0%_80%,rgba(59,130,246,0.18),transparent_32%),linear-gradient(135deg,#0f172a,#111827_55%,#172554)]">
            <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight text-white">AI WhatsApp</a>
                @if (Route::has('login'))
                    <nav class="flex items-center gap-3 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-cyan-400 px-4 py-2 font-semibold text-slate-950 transition hover:bg-cyan-300">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 font-medium text-slate-300 transition hover:text-white">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg border border-white/15 px-4 py-2 font-medium text-white transition hover:border-cyan-300/60">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="mx-auto grid min-h-[calc(100vh-88px)] w-full max-w-6xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
                <section>
                    <p class="mb-6 text-sm font-medium uppercase tracking-[0.24em] text-cyan-300">Customer conversations, amplified</p>
                    <h1 class="max-w-3xl text-5xl font-semibold leading-[1.05] tracking-tight text-white sm:text-7xl">Bangun percakapan yang terasa lebih manusiawi.</h1>
                    <p class="mt-7 max-w-xl text-lg leading-8 text-slate-300">AI WhatsApp membantu bisnis memahami customer, merangkum percakapan, dan merespons dengan lebih cepat dari satu workspace yang tenang.</p>
                    <div class="mt-9 flex flex-wrap gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-lg bg-cyan-400 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-300">Mulai gunakan</a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="rounded-lg border border-white/15 px-5 py-3 font-semibold text-white transition hover:border-cyan-300/60">Masuk ke workspace</a>
                        @endif
                    </div>
                </section>

                <section class="relative">
                    <div class="absolute -inset-8 rounded-full bg-cyan-400/10 blur-3xl"></div>
                    <div class="relative rounded-3xl border border-white/10 bg-slate-950/60 p-5 shadow-2xl shadow-slate-950/40 backdrop-blur sm:p-7">
                        <div class="flex items-center justify-between border-b border-white/10 pb-5">
                            <div>
                                <p class="text-sm text-slate-400">Live workspace</p>
                                <p class="mt-1 font-semibold text-white">Customer inbox</p>
                            </div>
                            <span class="flex items-center gap-2 rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-300"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Online</span>
                        </div>
                        <div class="space-y-4 py-6">
                            <div class="max-w-[85%] rounded-2xl rounded-bl-md bg-white/10 p-4 text-sm leading-6 text-slate-200">Halo, saya ingin mengetahui status pesanan saya.</div>
                            <div class="ml-auto max-w-[85%] rounded-2xl rounded-br-md bg-cyan-400 p-4 text-sm leading-6 text-slate-950">Tentu. Saya bantu cek sekarang. Mohon tunggu sebentar.</div>
                            <div class="rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-cyan-300">AI insight</p>
                                <p class="mt-2 text-sm leading-6 text-slate-200">Intent customer: <span class="font-semibold text-white">cek status pesanan</span></p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
