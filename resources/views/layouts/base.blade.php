<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AltoGestor' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --muted: #cbd5e1;
            --accent: #38bdf8;
            --accent-2: #818cf8;
        }
        body {
            background: radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.08), transparent 25%),
                        radial-gradient(circle at 80% 0%, rgba(129, 140, 248, 0.1), transparent 25%),
                        #0b1224;
            min-height: 100vh;
            color: #e2e8f0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        a { color: var(--accent); }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-8">
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-400 to-indigo-500 shadow-lg shadow-sky-900/30"></div>
                <div>
                    <p class="text-sm uppercase tracking-wide text-slate-400">AltoGestor</p>
                    <p class="text-lg font-semibold text-white">{{ $title ?? 'Dashboard' }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-300">
                <a href="/app" class="hover:text-white transition">App</a>
                <span class="mx-2 text-slate-500">•</span>
                <a href="/login" class="hover:text-white transition">Login</a>
            </div>
        </header>

        <main class="bg-[#0f172a]/70 border border-slate-800 rounded-2xl p-6 shadow-lg shadow-black/40">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
