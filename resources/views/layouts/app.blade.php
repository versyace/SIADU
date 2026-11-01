<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIADU SARANA') - SMK Negeri 5 Surakarta</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } }
    }
</script>    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-enter > * { animation: fadeUp .6s ease-out backwards; }
        .page-enter > *:nth-child(2) { animation-delay: .08s; }
        .page-enter > *:nth-child(3) { animation-delay: .16s; }
        .page-enter > *:nth-child(4) { animation-delay: .24s; }
        .page-enter > *:nth-child(n+5) { animation-delay: .32s; }
        @media (prefers-reduced-motion: reduce) {
            .page-enter > * { animation: none; }
        }
    </style>
</head>
<body class="bg-gradient-to-b from-green-50 via-white to-slate-50 min-h-screen flex flex-col antialiased">

    <header class="bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-3 sm:px-6 py-3 flex items-center justify-between gap-2">
            <a href="{{ route('pengaduan.create') }}" class="flex min-w-0 items-center gap-2 sm:gap-3">
                <img src="{{ asset('images/logo_smk.png') }}" alt="Logo SMK Negeri 5 Surakarta" class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 object-contain">
                <div class="leading-tight">
                    <p class="font-semibold text-sm sm:text-base text-slate-800 tracking-tight">SIADU SARANA</p>
                    <p class="text-xs text-slate-500">SMK Negeri 5 Surakarta</p>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                <a href="{{ route('pengaduan.create') }}"
                   class="pb-1 border-b-2 {{ request()->routeIs('pengaduan.create') ? 'text-green-700 border-green-600' : 'border-transparent hover:text-green-700' }} transition-colors">
                    Ajukan Pengaduan
                </a>
                <a href="{{ route('pengaduan.track.form') }}"
                   class="pb-1 border-b-2 {{ request()->routeIs('pengaduan.track.form') ? 'text-green-700 border-green-600' : 'border-transparent hover:text-green-700' }} transition-colors">
                    Lacak Laporan
                </a>
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('admin.login') }}"
                   class="hidden sm:inline-flex text-sm font-medium border border-slate-300 rounded-lg px-4 py-2 hover:bg-slate-50 hover:border-slate-400 transition-colors">
                    Masuk Admin
                </a>
                <button type="button" aria-label="Buka menu" onclick="toggleMenu()"
                        class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Menu samping (slide in dari kanan), hanya tampil di layar HP/tablet --}}
    <div id="menuOverlay" onclick="toggleMenu()"
         class="fixed inset-0 z-30 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

    <aside id="menuDrawer"
           class="fixed top-0 right-0 z-40 h-full w-72 max-w-[80%] bg-white shadow-2xl translate-x-full transition-transform duration-300 ease-out md:hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
            <p class="font-semibold text-slate-800">Menu</p>
            <button type="button" aria-label="Tutup menu" onclick="toggleMenu()"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="p-4 space-y-1 text-sm font-medium">
            <a href="{{ route('pengaduan.create') }}"
               class="block rounded-lg px-3 py-2.5 {{ request()->routeIs('pengaduan.create') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50' }}">
                Ajukan Pengaduan
            </a>
            <a href="{{ route('pengaduan.track.form') }}"
               class="block rounded-lg px-3 py-2.5 {{ request()->routeIs('pengaduan.track.form') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50' }}">
                Lacak Laporan
            </a>
            <a href="{{ route('admin.login') }}"
               class="block rounded-lg px-3 py-2.5 text-slate-600 hover:bg-slate-50">
                Masuk Admin
            </a>
        </nav>
    </aside>

    <main class="flex-1">
        <div class="page-enter max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 text-xs text-slate-400 text-center">
            &copy; {{ date('Y') }} SIADU SARANA &mdash; SMK Negeri 5 Surakarta. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const drawer = document.getElementById('menuDrawer');
            const overlay = document.getElementById('menuOverlay');
            const opening = drawer.classList.contains('translate-x-full');

            drawer.classList.toggle('translate-x-full', !opening);
            overlay.classList.toggle('opacity-0', !opening);
            overlay.classList.toggle('pointer-events-none', !opening);
        }
    </script>

</body>
</html>