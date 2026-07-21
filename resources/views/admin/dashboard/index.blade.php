@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <style>
        .dash-stat .stat-num { font-size: 30px; font-weight: 700; line-height: 1.1; color: #1f2a44; }
        .dash-stat .stat-label { font-size: 13px; font-weight: 500; color: #8a97ab; }
        .dash-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .imp-card .imp-fecha { font-size: 22px; font-weight: 700; color: #1f2a44; }
        .imp-card .imp-meta { font-size: 13px; color: #8a97ab; }
        .imp-card .imp-badge { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6"><h4>Gestión del Sistema</h4></div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg></a></li>
                        <li class="breadcrumb-item text-dark">Dashboard</li>
                        <li class="breadcrumb-item active">Gestión del Sistema</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid dashboard-2">
        {{-- ===== Tarjetas de estadísticas ===== --}}
        <div class="row">
            {{-- Cargados hoy --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="dash-stat">
                                <span class="stat-label">Cargados hoy</span>
                                <div class="stat-num" style="color:#4f5fbf">{{ $cargados['dia'] }}</div>
                            </div>
                            <div class="dash-icon" style="background:#eef1f6;color:#4f5fbf">
                                <i class="fa fa-calendar-check-o fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cargados esta semana --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="dash-stat">
                                <span class="stat-label">Cargados esta semana</span>
                                <div class="stat-num" style="color:#2f4b7c">{{ $cargados['semana'] }}</div>
                            </div>
                            <div class="dash-icon" style="background:#eaf0f8;color:#2f4b7c">
                                <i class="fa fa-calendar-o fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cargados este mes --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card small-widget">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="dash-stat">
                                <span class="stat-label">Cargados este mes</span>
                                <div class="stat-num" style="color:#2ba95f">{{ $cargados['mes'] }}</div>
                            </div>
                            <div class="dash-icon" style="background:#e5f6ea;color:#2ba95f">
                                <i class="fa fa-calendar fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendientes de revisión (todos los tiempos) --}}
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.trabajos.ordenes.index', ['estado' => \App\Enums\EstadoTrabajo::PENDIENTE->value]) }}" class="text-decoration-none">
                    <div class="card small-widget">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="dash-stat">
                                    <span class="stat-label">Pendientes de revisión</span>
                                    <div class="stat-num" style="color:#f0a020">{{ $pendientes }}</div>
                                </div>
                                <div class="dash-icon" style="background:#fdf1e3;color:#f0a020">
                                    <i class="fa fa-clock-o fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ===== Última importación Materiales / LPU ===== --}}
        <div class="row">
            @foreach ([['Materiales', $ultimaMateriales, '#4f5fbf'], ['LPU / Tipos de Trabajo', $ultimaLpu, '#e79f5f']] as [$titulo, $imp, $color])
            <div class="col-xl-6">
                <div class="card imp-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="imp-badge" style="color:{{ $color }}">Última importación · {{ $titulo }}</span>
                            <div class="dash-icon" style="background:{{ $color }}1a;color:{{ $color }}">
                                <i class="fa fa-upload"></i>
                            </div>
                        </div>
                        @if($imp)
                            <div class="imp-fecha">{{ $imp['fecha']->format('d/m/Y') }} <small class="text-muted" style="font-size:14px">{{ $imp['fecha']->format('H:i') }}</small></div>
                            <div class="imp-meta mt-1">
                                @if($imp['vigencia']) Vigencia: {{ $imp['vigencia']->format('d/m/Y') }} · @endif
                                {{ number_format($imp['procesados'], 0, ',', '.') }} registros
                                @if(!is_null($imp['nuevos'])) ({{ number_format($imp['nuevos'], 0, ',', '.') }} nuevos, {{ number_format($imp['actualizados'], 0, ',', '.') }} actualizados) @endif
                            </div>
                            @if($imp['usuario'])
                                <div class="imp-meta">Por: {{ $imp['usuario'] }}</div>
                            @endif
                        @else
                            <div class="imp-fecha text-muted" style="font-size:18px">Sin importaciones</div>
                            <div class="imp-meta mt-1">Todavía no se importó el Excel de {{ $titulo }}.</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ===== Gráficos ===== --}}
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h5 class="mb-0">Cargas por cuadrilla</h5>
                        <span class="f-12 text-muted">Mes en curso</span>
                    </div>
                    <div class="card-body">
                        <div id="chartCuadrillas"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h5 class="mb-0">Trabajos por estado</h5>
                        <span class="f-12 text-muted">Total histórico</span>
                    </div>
                    <div class="card-body">
                        <div id="chartEstados"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h5 class="mb-0">Cargas de los últimos 30 días</h5>
                    </div>
                    <div class="card-body">
                        <div id="chartTrend"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script>
        // ===== Datos desde el backend =====
        var cuadrillaLabels = @json($cuadrillaLabels);
        var cuadrillaData   = @json($cuadrillaData);
        var estadoLabels    = @json($estadoLabels);
        var estadoData      = @json($estadoData);
        var estadoColors    = @json($estadoColors);
        var trendLabels     = @json($trendLabels);
        var trendData       = @json($trendData);

        // ===== Cargas por cuadrilla (barras) =====
        new ApexCharts(document.querySelector('#chartCuadrillas'), {
            chart: { type: 'bar', height: 330, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [{ name: 'Trabajos', data: cuadrillaData }],
            xaxis: { categories: cuadrillaLabels },
            colors: ['#4f5fbf'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '45%', distributed: cuadrillaLabels.length <= 12 } },
            legend: { show: false },
            dataLabels: { enabled: true },
            noData: { text: 'Sin cargas este mes' },
            grid: { borderColor: '#eef1f4' }
        }).render();

        // ===== Trabajos por estado (donut) =====
        new ApexCharts(document.querySelector('#chartEstados'), {
            chart: { type: 'donut', height: 330, fontFamily: 'inherit' },
            series: estadoData,
            labels: estadoLabels,
            colors: estadoColors,
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            noData: { text: 'Sin trabajos' },
            plotOptions: { pie: { donut: { labels: { show: true, total: { show: true, label: 'Total' } } } } }
        }).render();

        // ===== Tendencia últimos 30 días (área) =====
        new ApexCharts(document.querySelector('#chartTrend'), {
            chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [{ name: 'Trabajos cargados', data: trendData }],
            xaxis: { categories: trendLabels, tickAmount: 10 },
            colors: ['#2ba95f'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef1f4' }
        }).render();
    </script>
@endsection
