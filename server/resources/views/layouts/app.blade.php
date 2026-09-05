<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'AI WhatsApp')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-full bg-slate-950 text-slate-100 antialiased">

    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(14,165,233,0.16),transparent_34%),linear-gradient(135deg,#0f172a_0%,#111827_52%,#172554_100%)]">
        @auth
            <nav class="border-b border-white/10 bg-slate-950/70 backdrop-blur">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4 lg:px-8">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold tracking-tight text-white transition hover:text-cyan-300">
                        AI WhatsApp
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="rounded-lg border border-white/15 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-300/60 hover:text-white">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        @endauth

        <main class="mx-auto w-full max-w-6xl px-6 py-10 lg:px-8 lg:py-14">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>