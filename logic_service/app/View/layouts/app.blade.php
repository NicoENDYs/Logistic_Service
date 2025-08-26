<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white border-b">
        <div class="mx-auto max-w-7xl px-4 h-14 flex items-center justify-between">
            <div class="font-semibold">Logística Agro</div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-sm text-gray-600 hover:text-gray-900">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="mx-auto max-w-7xl px-4 py-6 grid grid-cols-12 gap-6">
        <!-- Sidebar -->
        <aside class="col-span-12 md:col-span-3 lg:col-span-2">
            <div class="bg-white rounded-xl shadow p-3 space-y-1">
                <x-nav-link href="{{ route('vehicles.index') }}" :active="request()->routeIs('vehicles.*')">Vehículos</x-nav-link>
                <x-nav-link href="{{ route('drivers.index') }}" :active="request()->routeIs('drivers.*')">Choferes</x-nav-link>
                <x-nav-link href="{{ route('routes.index') }}" :active="request()->routeIs('routes.*')">Rutas</x-nav-link>
                <x-nav-link href="{{ route('trips.index') }}" :active="request()->routeIs('trips.*')">Viajes</x-nav-link>
            </div>
        </aside>

        <!-- Main -->
        <main class="col-span-12 md:col-span-9 lg:col-span-10">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
