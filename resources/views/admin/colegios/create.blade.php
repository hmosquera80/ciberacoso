<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Colegio - Panel Administrativo</title>
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
                        <h1><i class="fas fa-plus me-2"></i>Nuevo Colegio</h1>
                        <p class="text-muted">Agregar una nueva institución educativa al sistema</p>
                    </div>
                    <div>
                        <a href="{{ route('colegios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-school me-2"></i>Información del Colegio</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('colegios.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Nombre del Colegio <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}" 
                                       placeholder="Ingresa el nombre del colegio"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="municipio_id" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Municipio <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('municipio_id') is-invalid @enderror" 
                                        id="municipio_id" 
                                        name="municipio_id" 
                                        required>
                                    <option value="">Selecciona un municipio</option>
                                    @foreach($municipios as $municipio)
                                        <option value="{{ $municipio->id }}" {{ old('municipio_id') == $municipio->id ? 'selected' : '' }}>
                                            {{ $municipio->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('municipio_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> El colegio será asociado al municipio seleccionado
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="activo" 
                                           name="activo" 
                                           value="1" 
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <i class="fas fa-toggle-on me-1"></i>Colegio Activo
                                    </label>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Los colegios activos aparecerán disponibles en el sistema
                                    </div>
                                </div>
                            </div>

                            @if($municipios->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>¡Atención!</strong> No hay municipios disponibles. 
                                    <a href="{{ route('municipios.create') }}" class="alert-link">Crear municipio primero</a>.
                                </div>
                            @endif

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('colegios.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" {{ $municipios->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Guardar Colegio
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>