<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SKANJAMart'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-ink min-h-screen flex flex-col font-sans" x-data="{ navOpen: false }">

    <!-- ===== MOBILE: bar warna solid ala referensi (hamburger - logo - keranjang) ===== -->
    <header class="lg:hidden sticky top-0 z-40 bg-forest-700 text-canvas shadow-md">
        <div class="flex items-center justify-between px-4 h-16">
            <button @click="navOpen = true" aria-label="Buka menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('home') }}" class="font-display font-semibold text-lg tracking-tight">
                SKANJA<span class="text-brass-300 italic">Mart</span>
            </a>
            @auth
                @if(!auth()->user()->isAdmin())
                    <a href="{{ route('cart.index') }}" aria-label="Keranjang">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.87-4.788 2.202-7.391.083-.65-.421-1.226-1.075-1.226H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="w-8 h-8 rounded-full bg-brass-400 text-forest-700 flex items-center justify-center text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium">Masuk</a>
            @endauth
        </div>
    </header>

    <!-- Drawer menu mobile -->
    <div x-show="navOpen" x-cloak @click="navOpen = false" class="fixed inset-0 bg-black/40 z-40 lg:hidden"></div>
    <aside
        class="fixed inset-y-0 left-0 w-72 bg-white z-50 lg:hidden transform transition-transform duration-200 ease-in-out"
        :class="navOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="p-5 border-b border-forest-100 flex items-center justify-between">
            <span class="font-display font-semibold text-lg text-forest-700">SKANJA<span class="text-brass-500 italic">Mart</span></span>
            <button @click="navOpen = false" class="text-ink-faint hover:text-ink">✕</button>
        </div>
        <form action="{{ route('home') }}" method="GET" class="p-5 border-b border-forest-100">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk favoritmu..." class="input-classic text-sm">
        </form>
        <nav class="flex flex-col text-sm font-medium text-ink-muted">
            <a href="{{ route('home') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Beranda</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Dashboard Admin</a>
                @else
                    <a href="{{ route('dashboard') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Dashboard</a>
                    <a href="{{ route('orders.index') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Pesanan</a>
                    <a href="{{ route('cart.index') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Keranjang</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">{{ auth()->user()->name }}</a>
                <form action="{{ route('logout') }}" method="POST" class="px-5 py-3.5 border-t border-forest-100 mt-2">
                    @csrf
                    <button class="text-wine-500 hover:text-wine-600 transition">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Masuk</a>
                <a href="{{ route('register') }}" class="px-5 py-3.5 hover:bg-forest-50 hover:text-forest-600 transition">Daftar</a>
            @endauth
        </nav>
    </aside>

    <!-- ===== DESKTOP: header lama tetap dipakai ===== -->
    <header class="hidden lg:block bg-canvas/90 backdrop-blur border-b border-forest-100 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-6">
                <a href="{{ route('home') }}" class="font-display font-semibold text-2xl text-forest-700 tracking-tight flex-shrink-0">
                    SKANJA<span class="text-brass-500 italic">Mart</span>
                </a>

                <form action="{{ route('home') }}" method="GET" class="flex flex-1 max-w-md">
                    <div class="relative w-full">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk favoritmu..."
                            class="input-classic pl-4 pr-4 py-2.5 text-sm placeholder:text-ink-faint">
                    </div>
                </form>

                <nav class="flex items-center gap-5 text-sm font-medium text-ink-muted">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-forest-600 transition">Dashboard Admin</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="hover:text-forest-600 transition">Dashboard</a>
                            <a href="{{ route('orders.index') }}" class="hover:text-forest-600 transition">Pesanan</a>
                            <a href="{{ route('cart.index') }}" class="hover:text-forest-600 transition">Keranjang</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="text-ink-faint hover:text-forest-600 transition">{{ auth()->user()->name }}</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="text-wine-500 hover:text-wine-600 transition">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-forest-600 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary !py-2 !px-4">Daftar</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-forest-50 border border-forest-100 text-forest-700 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-xl bg-wine-500/10 border border-wine-500/20 text-wine-600 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-forest-700 text-canvas/70 text-sm mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col sm:flex-row justify-between gap-3">
            <span class="font-display text-canvas text-lg">SKANJA<span class="text-brass-400 italic">Mart</span></span>
            <div class="flex flex-col sm:items-end gap-1">
                <span>&copy; {{ date('Y') }} SKANJAMart. Semua hak dilindungi.</span>
                <span class="text-canvas/50">Belanja gampang, sampai dengan aman.</span>
            </div>
        </div>
    </footer>

    @include('partials.chatbot-widget')
    @stack('scripts')
</body>
</html>
