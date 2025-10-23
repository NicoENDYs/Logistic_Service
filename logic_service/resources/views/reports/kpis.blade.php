@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <header class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-emerald-700">🌱 Indicadores de Envios — Agroindustrial</h1>
        <p class="text-sm text-gray-500 mt-1">Resumen rápido de Envios: Envios, pendientes y fallidas.</p>
    </header>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-start">
            <span class="text-xs text-gray-400">Total Entregas</span>
            <div class="mt-2 flex items-center justify-between w-full">
                <div>
                    <p class="text-2xl font-bold text-emerald-700">{{ number_format($total) }}</p>
                    <p class="text-xs text-gray-400">Total acumulado</p>
                </div>
                <!-- truck icon -->
                <svg class="w-10 h-10 text-emerald-200" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <path d="M3 7h11v6H3z" fill="currentColor" opacity="0.12"/>
                    <path d="M3 7h11l3 3h4v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="7.5" cy="17.5" r="1.5" fill="currentColor"/>
                    <circle cx="17.5" cy="17.5" r="1.5" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-start">
            <span class="text-xs text-gray-400">Entregadas</span>
            <div class="mt-2 flex items-center justify-between w-full">
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ number_format($deliveried) }}</p>
                    <p class="text-xs text-gray-400">
                        ({{ $porcentaje ?? round(($deliveried / max($total,1)) * 100, 2) }}%)
                    </p>
                </div>
                <!-- check icon -->
                <svg class="w-10 h-10 text-emerald-100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-start">
            <span class="text-xs text-gray-400">Pendientes</span>
            <div class="mt-2 flex items-center justify-between w-full">
                <div>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($pending) }}</p>
                    <p class="text-xs text-gray-400">En proceso</p>
                </div>
                <!-- hourglass icon -->
                <svg class="w-10 h-10 text-yellow-100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <path d="M7 2h10M7 22h10M12 8v8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-start">
            <span class="text-xs text-gray-400">Fallidas</span>
            <div class="mt-2 flex items-center justify-between w-full">
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($failed) }}</p>
                    <p class="text-xs text-gray-400">Errores / Rechazadas</p>
                </div>
                <!-- x icon -->
                <svg class="w-10 h-10 text-red-100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts section -->
    <section class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pie / doughnut -->
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-sm font-semibold text-emerald-700 mb-3">Distribución de Estados</h2>
            <div class="w-full h-64 sm:h-72 md:h-80">
                <canvas id="doughnutChart" class="w-full h-full"></canvas>
            </div>
            <div class="mt-3 text-xs text-gray-500">Proporción entre entregadas, pendientes y fallidas.</div>
        </div>

        <!-- Bar chart -->
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-sm font-semibold text-emerald-700 mb-3">Comparativa por Estado</h2>
            <div class="w-full h-64 sm:h-72 md:h-80">
                <canvas id="barChart" class="w-full h-full"></canvas>
            </div>
            <div class="mt-3 text-xs text-gray-500">Comparación visual de cantidades.</div>
        </div>
    </section>

    <!-- Actions -->
    <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('report.excel') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700">
            <!-- simple excel svg -->
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"><path d="M4 3h10l6 6v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><path d="M8 13l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
            Exportar Excel
        </a>

        <a href="{{ route('report.pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"><path d="M6 2h7l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
            Exportar PDF
        </a>
    </div>
</div>

<!-- Chart.js (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Inyectamos datos desde Blade de forma segura -->
<div id="chart-data"
     data-deliveried="{{ $deliveried ?? 0 }}"
     data-pending="{{ $pending ?? 0 }}"
     data-failed="{{ $failed ?? 0 }}">
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Leer datos desde el div
    const chartDiv = document.getElementById('chart-data');
    const deliveried = Number(chartDiv.dataset.deliveried || 0);
    const pending = Number(chartDiv.dataset.pending || 0);
    const failed = Number(chartDiv.dataset.failed || 0);

    const values = [deliveried, pending, failed];
    const labels = ['Entregadas', 'Pendientes', 'Fallidas'];

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 10,
                    padding: 12,
                    usePointStyle: true,
                    font: { size: 12 }
                }
            }
        }
    };

    // Gráfico Doughnut
    const doughnutEl = document.getElementById('doughnutChart');
    if (doughnutEl) {
        new Chart(doughnutEl, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#16a34a', '#f59e0b', '#ef4444'],
                    hoverOffset: 6
                }]
            },
            options: commonOptions
        });
    }

    // Gráfico de Barras
    const barEl = document.getElementById('barChart');
    if (barEl) {
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad',
                    data: values,
                    backgroundColor: ['#059669', '#d97706', '#dc2626'],
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.6
                }]
            },
            options: Object.assign({}, commonOptions, {
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' }
                    }
                }
            })
        });
    }
});
</script>

@endsection
