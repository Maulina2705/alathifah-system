<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Master Raport Tahfizh Al-Athifa' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Anti-flicker Early Theme Script -->
    <script>
        (function() {
            try {
                const mode = localStorage.getItem('alathifah_theme_mode') || 'light';
                const isDark = mode === 'dark' || (mode === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch(e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden transition-colors duration-200">

    <!-- Background Ambient Glow (Pink & Yellow Accents) -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-pink-500/15 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-amber-400/15 blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        @yield('content')
    </div>

</body>
</html>
