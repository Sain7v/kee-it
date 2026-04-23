<x-app-layout>

<x-slot name="header">
    <h1 class="text-xl font-bold text-gray-900">Estadísticas</h1>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Summary row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Racha actual</p>
            <p class="mt-1 text-3xl font-bold text-brand-600">{{ $streak['current'] }} 🔥</p>
            <p class="mt-1 text-xs text-gray-400">mejor: {{ $streak['best'] }} días</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">A tiempo</p>
            <p class="mt-1 text-3xl font-bold text-green-600">{{ $onTimeRate }}%</p>
            <p class="mt-1 text-xs text-gray-400">tareas completadas a tiempo</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Completadas (4 sem.)</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ collect($weeklyCompleted)->sum('total') }}</p>
            <p class="mt-1 text-xs text-gray-400">últimas 4 semanas</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Logros</p>
            <p class="mt-1 text-3xl font-bold text-yellow-500">{{ count($achievements) }}</p>
            <p class="mt-1 text-xs text-gray-400">desbloqueados</p>
        </div>

    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Weekly completed chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Tareas completadas por semana</h2>
            <div class="h-48">
                <canvas id="chartCompleted"></canvas>
            </div>
        </div>

        {{-- Category distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Por categoría</h2>
            @if(empty($categoryDistribution))
                <p class="text-xs text-gray-400 text-center py-8">Sin datos.</p>
            @else
                <div class="h-48 flex items-center justify-center">
                    <canvas id="chartCategories"></canvas>
                </div>
            @endif
        </div>

    </div>

    {{-- Heatmap (30 days) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Actividad últimos 30 días</h2>
        <div class="grid grid-cols-15 gap-1" style="grid-template-columns: repeat({{ min(30, count($heatmap)) }}, minmax(0, 1fr))">
            @foreach($heatmap as $day)
                @php
                    $intensity = match(true) {
                        $day['tasks_completed'] >= 5 => 'bg-brand-600',
                        $day['tasks_completed'] >= 3 => 'bg-brand-400',
                        $day['tasks_completed'] >= 1 => 'bg-brand-200',
                        default                       => 'bg-gray-100',
                    };
                @endphp
                <div class="{{ $intensity }} h-5 rounded-sm"
                     title="{{ $day['date'] }}: {{ $day['tasks_completed'] }} tarea(s)"></div>
            @endforeach
        </div>
        <div class="flex items-center gap-2 mt-3 text-xs text-gray-400">
            <span>Menos</span>
            <span class="w-4 h-3 rounded-sm bg-gray-100"></span>
            <span class="w-4 h-3 rounded-sm bg-brand-200"></span>
            <span class="w-4 h-3 rounded-sm bg-brand-400"></span>
            <span class="w-4 h-3 rounded-sm bg-brand-600"></span>
            <span>Más</span>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Achievements --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Logros</h2>
            @php
                $allAchievements = [
                    'primera_llama'  => ['🔥', 'Primera llama',    '3 días de racha'],
                    'semana_de_fuego'=> ['🌟', 'Semana de fuego',  '7 días de racha'],
                    'imparable'      => ['⚡', 'Imparable',        '14 días de racha'],
                    'leyenda'        => ['👑', 'Leyenda',          '30 días de racha'],
                ];
            @endphp
            <div class="grid grid-cols-2 gap-3">
                @foreach($allAchievements as $key => [$icon, $name, $desc])
                    @php $unlocked = isset($achievements[$key]); @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl border {{ $unlocked ? 'border-yellow-200 bg-yellow-50' : 'border-gray-100 bg-gray-50 opacity-50' }}">
                        <span class="text-2xl">{{ $icon }}</span>
                        <div>
                            <p class="text-xs font-semibold {{ $unlocked ? 'text-yellow-800' : 'text-gray-500' }}">{{ $name }}</p>
                            <p class="text-[11px] text-gray-400">{{ $desc }}</p>
                            @if($unlocked)
                                <p class="text-[10px] text-yellow-600 mt-0.5">
                                    {{ \Carbon\Carbon::parse($achievements[$key])->locale('es')->isoFormat('D MMM YYYY') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- AI Insight --}}
        <div class="bg-white rounded-xl border border-brand-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-brand-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.2 3.2 0 01-2.19.953H9.68a3.2 3.2 0 01-2.19-.953l-.347-.347z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-900">Análisis de productividad (IA)</h2>
            </div>

            @if($aiInsight)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $aiInsight }}</p>
            @else
                <p class="text-sm text-gray-400">Sin análisis disponible. Configura tu API key de Anthropic en el archivo .env.</p>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const completedData = @json(collect($weeklyCompleted)->pluck('total')->values());
const completedLabels = @json(collect($weeklyCompleted)->map(fn($r) => 'Sem. ' . substr($r->yw, 4))->values());

const catLabels = @json(array_keys($categoryDistribution));
const catData   = @json(array_values($categoryDistribution));

// Bar chart
const ctx1 = document.getElementById('chartCompleted');
if (ctx1) {
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: completedLabels.length ? completedLabels : ['Sin datos'],
            datasets: [{
                label: 'Completadas',
                data: completedData.length ? completedData : [0],
                backgroundColor: '#6366f1',
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
}

// Doughnut chart
const ctx2 = document.getElementById('chartCategories');
if (ctx2 && catLabels.length) {
    const colors = ['#6366f1','#f59e0b','#10b981','#ef4444','#8b5cf6'];
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData,
                backgroundColor: colors.slice(0, catLabels.length),
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } },
            },
        },
    });
}
</script>
@endpush

</x-app-layout>
