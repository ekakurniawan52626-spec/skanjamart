<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - SKANJAMart')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas-soft text-ink min-h-screen flex font-sans" x-data="{ sidebarOpen: false }">

    <!-- Overlay gelap pas sidebar mobile kebuka -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

    <aside
        class="w-64 bg-forest-700 text-canvas/80 flex-shrink-0 min-h-screen fixed inset-y-0 left-0 z-40 transform transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 lg:static"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="px-6 py-6 font-display text-lg text-canvas flex items-center justify-between">
            <span>SKANJA<span class="text-brass-400 italic">Mart</span>
            <span class="text-xs font-sans font-normal text-canvas/50 block mt-0.5 tracking-wide">Admin Panel</span></span>
            <button @click="sidebarOpen = false" class="lg:hidden text-canvas/70 hover:text-canvas">✕</button>
        </div>
        <nav class="mt-2 flex flex-col text-sm">
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.dashboard') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.products.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Produk</a>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.categories.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Kategori</a>
            <a href="{{ route('admin.orders.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.orders.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Pesanan</a>
            <a href="{{ route('admin.deliveries.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.deliveries.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Pengiriman</a>
            <a href="{{ route('admin.couriers.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.couriers.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Kurir</a>
            <a href="{{ route('admin.faqs.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.faqs.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Chatbot</a>
            <a href="{{ route('admin.reports.index') }}" class="px-6 py-3 hover:bg-forest-600 transition {{ request()->routeIs('admin.reports.*') ? 'bg-forest-600 text-canvas border-l-4 border-brass-400' : '' }}">Laporan</a>
            <a href="{{ route('home') }}" class="px-6 py-3 hover:bg-forest-600 transition mt-4 border-t border-canvas/10">Lihat Toko</a>
            <form action="{{ route('logout') }}" method="POST" class="px-6 py-3">
                @csrf
                <button class="hover:text-brass-300 transition">Keluar</button>
            </form>
        </nav>
    </aside>

    <div class="flex-1 min-w-0">
        <header class="bg-canvas/90 backdrop-blur border-b border-forest-100 px-4 sm:px-6 py-4 flex items-center gap-3 justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden text-ink shrink-0" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="font-display font-medium text-lg text-ink truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <span class="text-sm text-ink-muted shrink-0">Halo, {{ auth()->user()->name }}</span>
        </header>
        <div class="p-4 sm:p-6">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-forest-50 border border-forest-100 text-forest-700 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
