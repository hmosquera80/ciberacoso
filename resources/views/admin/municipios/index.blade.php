<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Municipios - Panel Administrativo</title>
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
        .table thead th {
            background-color: #343a40;
            color: white;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-map-marker-alt me-2"></i>Gestión de Municipios</h1>
                        <p class="text-muted">Administra los municipios del sistema</p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                        <a href="{{ route('municipios.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Municipio
                        </a>
                    </div>
                </div>
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list me-2"></i>Lista de Municipios</h5>
                    </div>
                    <div class="card-body">
                        @if ($municipios->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay municipios registrados</h5>
                                <p class="text-muted">Comienza agregando el primer municipio al sistema.</p>
                                <a href="{{ route('municipios.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Municipio
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Estado</th>
                                            <th>Fecha de Creación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($municipios as $municipio)
                                            <tr>
                                                <td><strong>#{{ $municipio->id }}</strong></td>
                                                <td>{{ $municipio->nombre }}</td>
                                                <td>
                                                    @if($municipio->activo)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle"></i> Activo
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle"></i> Inactivo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $municipio->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('municipios.edit', $municipio->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('municipios.destroy', $municipio->id) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Estás seguro de eliminar este municipio?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $municipios->links() }}
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