<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bienvenido al Sistema de Logística') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 mb-4">Selecciona una de las siguientes opciones para comenzar:</p>
                </div>
            </div>

            <!-- Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Drivers Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h4 class="text-lg font-semibold">Conductores</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Gestiona la información de los conductores</p>
                        <a href="{{ route('drivers.index') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Conductores
                        </a>
                    </div>
                </div>

                <!-- Vehicles Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-lg font-semibold">Vehículos</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Administra la flota de vehículos</p>
                        <a href="{{ route('vehicles.index') }}"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Vehículos
                        </a>
                    </div>
                </div>

                <!-- Routes Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                </path>
                            </svg>
                            <h4 class="text-lg font-semibold">Rutas</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Planifica y gestiona las rutas</p>
                        <a href="{{ route('routes.index') }}"
                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Rutas
                        </a>
                    </div>
                </div>

                <!-- Trips Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-lg font-semibold">Viajes</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Monitorea los viajes en curso</p>
                        <a href="{{ route('trips.index') }}"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Viajes
                        </a>
                    </div>
                </div>

                <!-- Deliveries Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h4 class="text-lg font-semibold">Entregas</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Gestiona las entregas programadas</p>
                        <a href="{{ route('deliveries.index') }}"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Entregas
                        </a>
                    </div>
                </div>

                <!-- Incidents Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-orange-500 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <h4 class="text-lg font-semibold">Incidentes</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Reporta y gestiona incidentes</p>
                        <a href="{{ route('incidents.index') }}"
                            class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Ver Incidentes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Resumen del Sistema</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ App\Models\Driver::count() }}</div>
                            <div class="text-gray-600">Conductores</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ App\Models\Vehicle::count() }}</div>
                            <div class="text-gray-600">Vehículos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ App\Models\Route::count() }}</div>
                            <div class="text-gray-600">Rutas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ App\Models\Trip::where('status', 'active')->count() }}</div>
                            <div class="text-gray-600">Viajes Activos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
