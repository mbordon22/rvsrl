@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-8"><h4>{{ $periodo->nombre }}</h4></div>
            <div class="col-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.trabajos.periodos.index') }}">Certificación</a></li>
                    <li class="breadcrumb-item active">Período</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Barra superior --}}
    <div class="card">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="badge bg-info">{{ ucfirst($periodo->estado) }}</span>
                <strong class="ms-2">{{ $periodo->fecha_desde->format('d/m/Y') }} — {{ $periodo->fecha_hasta->format('d/m/Y') }}</strong>
                <span class="text-muted ms-2">| {{ $periodo->cuadrilla?->nombre ?? 'Todas las cuadrillas' }}</span>
                <span class="badge bg-dark ms-2">{{ $periodo->categoria->label() }}</span>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.trabajos.periodos.asignar', $periodo->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-primary btn-sm" type="submit" title="Buscar y asignar trabajos nuevos del rango">
                        <i data-feather="refresh-cw"></i> Asignar trabajos
                    </button>
                </form>
                <form action="{{ route('admin.trabajos.periodos.cerrar', $periodo->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                        {{ $periodo->estado === 'abierto' ? 'Cerrar período' : 'Reabrir' }}
                    </button>
                </form>
                <a href="{{ route('admin.trabajos.periodos.showExportar', $periodo->id) }}" class="btn btn-success btn-sm">
                    <i data-feather="download"></i> Generar Excel
                </a>
            </div>
        </div>
    </div>

    @if($sinLpu > 0)
        <div class="alert alert-warning">
            <i data-feather="alert-triangle"></i> Hay <strong>{{ $sinLpu }}</strong> trabajo(s) sin código LPU asignado.
            Revisalos con "Ajustar" para que entren en la certificación.
        </div>
    @endif

    <div class="row">
        {{-- Resumen CERTIFICACION (LPU) --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Certificación (por LPU)</h5>
                    <span class="badge bg-success fs-6">Total: $ {{ number_format($totalCertificado, 2, ',', '.') }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>LPU</th><th>Descripción</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @forelse($lpuResumen as $r)
                                <tr>
                                    <td>{{ $r['lpu']->codigo_lpu }}</td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($r['lpu']->descripcion, 32) }}</td>
                                    <td class="text-center">{{ $r['cantidad'] }}</td>
                                    <td class="text-end">{{ number_format($r['precio'], 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($r['subtotal'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Sin trabajos con LPU</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Resumen CONSUMOS (materiales) --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Consumos (materiales)</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Código</th><th>Material</th><th class="text-center">Cant. total</th></tr>
                        </thead>
                        <tbody>
                            @forelse($materialesResumen as $r)
                                <tr>
                                    <td>{{ $r['material']?->codigo }}</td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($r['material']?->descripcion, 36) }}</td>
                                    <td class="text-center">{{ rtrim(rtrim(number_format($r['cantidad'], 2, ',', '.'), '0'), ',') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Sin materiales</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Metadatos de la certificación --}}
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Datos de la certificación (para el Excel)</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.trabajos.periodos.updateMeta', $periodo->id) }}" method="POST" class="row g-3">
                @csrf @method('PUT')
                <div class="col-md-6"><label class="form-label">Obra / Tarea</label><input type="text" name="obra" class="form-control" value="{{ old('obra', $periodo->obra) }}"></div>
                <div class="col-md-3"><label class="form-label">PEP / OC</label><input type="text" name="pep" class="form-control" value="{{ old('pep', $periodo->pep) }}"></div>
                <div class="col-md-3"><label class="form-label">Certif. N°</label><input type="text" name="certif_numero" class="form-control" value="{{ old('certif_numero', $periodo->certif_numero) }}"></div>
                <div class="col-12"><label class="form-label">Descripción</label><input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $periodo->descripcion) }}"></div>
                <div class="col-md-4"><label class="form-label">Supervisión TECO</label><input type="text" name="supervisor_teco" class="form-control" value="{{ old('supervisor_teco', $periodo->supervisor_teco) }}"></div>
                <div class="col-md-4"><label class="form-label">Contratista</label><input type="text" name="contratista" class="form-control" value="{{ old('contratista', $periodo->contratista) }}"></div>
                <div class="col-md-4">
                    <label class="form-label">Categoría (precio)</label>
                    <select name="categoria" class="form-select">
                        <option value="mantenimiento" {{ $periodo->categoria->value === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="obras" {{ $periodo->categoria->value === 'obras' ? 'selected' : '' }}>Obras</option>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Inicio de obra</label><input type="date" name="fecha_inicio_obra" class="form-control" value="{{ old('fecha_inicio_obra', $periodo->fecha_inicio_obra?->format('Y-m-d')) }}"></div>
                <div class="col-md-6"><label class="form-label">Fin de obra</label><input type="date" name="fecha_fin_obra" class="form-control" value="{{ old('fecha_fin_obra', $periodo->fecha_fin_obra?->format('Y-m-d')) }}"></div>
                <div class="col-12 text-end"><button type="submit" class="btn btn-primary">Guardar datos</button></div>
            </form>
        </div>
    </div>

    {{-- Trabajos asignados --}}
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Trabajos del período ({{ $trabajos->count() }})</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Fecha</th><th>Cuadrilla</th><th>Domicilio</th><th>Poste</th><th>LPU</th><th class="text-center">Materiales</th><th class="text-center">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse($trabajos as $t)
                            <tr>
                                <td>{{ $t->fecha->format('d/m/Y') }}</td>
                                <td>{{ $t->cuadrilla?->nombre }}</td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($t->domicilio, 25) ?: '—' }}</td>
                                <td>{{ $t->tipo_poste?->label() ?? '—' }}</td>
                                <td>
                                    @if($t->lpu)
                                        {{ $t->lpu->codigo_lpu }}
                                    @else
                                        <span class="badge bg-warning">sin LPU</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $t->materiales->count() }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.trabajos.periodos.ajustar', [$periodo->id, $t->id]) }}" class="btn btn-sm btn-outline-primary py-0" title="Ajustar LPU y materiales"><i data-feather="edit-2" style="width:14px;"></i></a>
                                    <form action="{{ route('admin.trabajos.periodos.quitarTrabajo', [$periodo->id, $t->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Quitar este trabajo del período?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0" title="Quitar del período"><i data-feather="x" style="width:14px;"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted p-3">No hay trabajos asignados. Usá "Asignar trabajos" o cargá trabajos en el rango de fechas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script> feather.replace(); </script>
@endsection
