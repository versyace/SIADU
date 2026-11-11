<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin') - SIADU</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo_smk.png') }}">    
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
<body class="@yield('body-class', 'bg-gradient-to-b from-green-50 via-white to-slate-50') min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo_smk.png') }}" alt="Logo SMK Negeri 5 Surakarta" class="w-9 h-9 object-contain">
                <div class="leading-tight">
                    <p class="font-semibold text-slate-800">SIADU</p>
                    <p class="text-xs text-slate-500">Dashboard Admin</p>
                </div>
            </a>

            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium border border-slate-300 rounded-lg px-4 py-2 hover:bg-slate-50">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <div class="page-enter max-w-6xl mx-auto px-4 py-8">
            @unless (request()->routeIs('admin.dashboard'))
                @foreach (['success' => 'green', 'warning' => 'amber', 'error' => 'red'] as $messageType => $messageColor)
                    @if (session($messageType))
                        <div role="{{ $messageType === 'error' ? 'alert' : 'status' }}"
                             class="mb-4 rounded-lg border border-{{ $messageColor }}-200 bg-{{ $messageColor }}-50 px-4 py-3 text-sm text-{{ $messageColor }}-800">
                            {{ session($messageType) }}
                        </div>
                    @endif
                @endforeach
            @endunless
            @yield('content')
        </div>
    </main>

</body>
</html>