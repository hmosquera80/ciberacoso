<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-body h4 {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .card-body p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .btn-outline-primary, .btn-outline-success, .btn-outline-info, .btn-outline-warning, .btn-outline-secondary {
            border-width: 2px;
            font-size: 1.1rem;
            padding: 1.5rem 1rem;
        }
        .btn-outline-primary { color: #0d6efd; border-color: #0d6efd; }
        .btn-outline-success { color: #198754; border-color: #198754; }
        .btn-outline-info { color: #0dcaf0; border-color: #0dcaf0; }
        .btn-outline-warning { color: #ffc107; border-color: #ffc107; }
        .btn-outline-secondary { color: #6c757d; border-color: #6c757d; }

        .btn-outline-primary:hover { background-color: #0d6efd; color: white; }
        .btn-outline-success:hover { background-color: #198754; color: white; }
        .btn-outline-info:hover { background-color: #0dcaf0; color: white; }
        .btn-outline-warning:hover { background-color: #ffc107; color: white; }
        .btn-outline-secondary:hover { background-color: #6c757d; color: white; }
        
        /* Nuevos estilos para el color naranja (Pendiente de Cierre) */
        .bg-orange {
            background-color: #fd7e14 !important; /* Un naranja de Bootstrap */
        }
        .text-orange {
            color: #fd7e14 !important;
        }
        /* Fin nuevos estilos */

        /* Estilos para las tarjetas de estadísticas en una sola línea */
        .stats-row {
            display: flex;
            flex-wrap: nowrap; /* Evita que las tarjetas salten de línea */
            overflow-x: auto; /* Permite desplazamiento horizontal si no caben */
            gap: 15px; /* Espacio entre las tarjetas */
        }
        .stats-row > div {
            flex: 0 0 calc(20% - 15px); /* 5 tarjetas en una línea, con espaciado */
            min-width: 180px; /* Ancho mínimo para que no se hagan demasiado pequeñas */
        }
        /* Fin estilos para tarjetas */


        .table thead th {
            background-color: #343a40;
            color: white;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
        }

        /* Responsive adjustments for stats cards */
        @media (max-width: 767.98px) { /* Small devices (portrait tablets and large phones) */
            .stats-row {
                flex-wrap: wrap; /* Allow cards to wrap on smaller screens */
                justify-content: center; /* Center cards when they wrap */
            }
            .stats-row > div {
                flex: 0 0 calc(50% - 15px); /* 2 cards per row on small screens */
                max-width: calc(50% - 15px);
            }
        }
        @media (max-width: 575.98px) { /* Extra small devices (phones) */
            .stats-row > div {
                flex: 0 0 100%; /* 1 card per row on extra small screens */
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>Panel Administrativo
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-white">
                    <i class="fas fa-user"></i> Bienvenido, {{ Auth::user()->name }}
                    ({{ str_replace('_', ' ', Auth::user()->role) }})
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
                <p class="text-muted">
                    Bienvenido al panel administrativo del sistema de denuncias
                    @if(Auth::user()->colegio)
                        - {{ Auth::user()->colegio->nombre }}
                        @if(Auth::user()->colegio->municipio)
                            ({{ Auth::user()->colegio->municipio->nombre }})
                        @endif
                    @else
                        - Sistema Global
                    @endif
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4 stats-row"> {{-- Agregada la clase stats-row aquí --}}
            @php
                $total_denuncias = $stats['total_denuncias'] ?? 0;
                $denuncias_abiertas = $stats['denuncias_abiertas'] ?? 0;
                $denuncias_en_tramite = $stats['denuncias_en_tramite'] ?? 0;
                $denuncias_pendiente_cierre = $stats['denuncias_pendiente_cierre'] ?? 0;
                $denuncias_cerradas = $stats['denuncias_cerradas'] ?? 0;
            @endphp

            <div> {{-- Eliminado col-md-3, usando el estilo de stats-row --}}
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $total_denuncias }}</h4>
                                <p class="mb-0">Total Denuncias</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div> {{-- Eliminado col-md-3 --}}
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $denuncias_abiertas }}</h4>
                                <p class="mb-0">Abiertas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-folder-open fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div> {{-- Eliminado col-md-3 --}}
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $denuncias_en_tramite }}</h4>
                                <p class="mb-0">En Trámite</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- NUEVA TARJETA: Pendiente de Cierre --}}
            <div> {{-- Eliminado col-md-3 --}}
                <div class="card bg-orange text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $denuncias_pendiente_cierre }}</h4>
                                <p class="mb-0">Pendiente de Cierre</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FIN NUEVA TARJETA --}}
            <div> {{-- Eliminado col-md-3 --}}
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $denuncias_cerradas }}</h4>
                                <p class="mb-0">Cerradas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-th-large me-2"></i>Menú Principal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(Auth::user()->role === 'super_admin')
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                                        <strong>Gestionar Usuarios</strong>
                                        <br><small>Administrar usuarios del sistema</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('municipios.index') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-map-marker-alt fa-2x d-block mb-2"></i>
                                        <strong>Gestionar Municipios</strong>
                                        <br><small>Administrar municipios del sistema</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('colegios.index') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-school fa-2x d-block mb-2"></i>
                                        <strong>Gestionar Colegios</strong>
                                        <br><small>Administrar instituciones educativas</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                        <strong>Reportes Generales</strong>
                                        <br><small>Estadísticas y análisis globales</small>
                                    </a>
                                </div>
                            @endif

                            @if(Auth::user()->role === 'admin')
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('my-users.create-supervisor') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                                        <strong>Crear Supervisor</strong>
                                        <br><small>Supervisores de tu colegio</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-list fa-2x d-block mb-2"></i>
                                        <strong>Ver Denuncias (Mi Colegio)</strong>
                                        <br><small>Gestionar casos reportados en tu entidad</small>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('admin.reports.entity_index') }}" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                        <strong>Reportes (Mi Colegio)</strong>
                                        <br><small>Estadísticas y análisis de tu entidad</small>
                                    </a>
                                </div>
                            @endif

                            @if(Auth::user()->role === 'supervisor')
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-list fa-2x d-block mb-2"></i>
                                        <strong>Ver Mis Denuncias</strong>
                                        <br><small>Seguimiento de casos asignados</small>
                                    </a>
                                </div>
                            @endif
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('report.create') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                                    <strong>Crear Nueva Denuncia</strong>
                                    <br><small>Acceso al formulario público de reporte</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock me-2"></i>Denuncias Recientes</h5>
                    </div>
                    <div class="card-body">
                        @if ($reports->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay denuncias registradas aún o no tienes permiso para verlas.</h5>
                                <p class="text-muted">Las denuncias aparecerán aquí cuando se reporten casos.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Denunciante</th>
                                            <th>Colegio</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                            <tr>
                                                <td><strong>#{{ $report->id }}</strong></td>
                                                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $report->denunciante_nombre_completo ?? 'Anónimo' }}</td>
                                                <td>{{ $report->denunciante_colegio }}</td>
                                                <td>
                                                    @php
                                                        $badgeClass = '';
                                                        switch ($report->estado->nombre) {
                                                            case 'Abierta': $badgeClass = 'bg-danger'; break;
                                                            case 'En Trámite': $badgeClass = 'bg-warning'; break;
                                                            case 'Pendiente de Cierre': $badgeClass = 'bg-info'; break;
                                                            case 'Cerrada': $badgeClass = 'bg-success'; break;
                                                            default: $badgeClass = 'bg-secondary'; break;
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        <i class="fas fa-circle-info"></i>
                                                        {{ $report->estado->nombre }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('reports.show', $report->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $reports->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>