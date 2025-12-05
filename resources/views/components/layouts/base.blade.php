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
    <div class="container" style="padding: 24px 16px;">
        <main>
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
