<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Supervisor - Panel Administrativo</title>
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
                        <h1><i class="fas fa-user-plus me-2"></i>Crear Supervisor</h1>
                        <p class="text-muted">
                            Agregar un nuevo supervisor para: 
                            <strong>{{ $adminColegio ? $adminColegio->nombre : 'Tu Colegio' }}</strong>
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                @if($adminColegio)
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-check me-2"></i>Nuevo Supervisor</h5>
                        </div>
                        <div class="card-body">
                            <!-- Información del colegio asignado -->
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-school fa-2x me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Colegio Asignado</h6>
                                        <strong>{{ $adminColegio->nombre }}</strong>
                                        @if($adminColegio->municipio)
                                            <br><small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $adminColegio->municipio->nombre }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <small class="mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Este supervisor se asignará automáticamente a tu colegio y podrá gestionar las denuncias de esta institución.
                                </small>
                            </div>

                            <form action="{{ route('my-users.store-supervisor') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nombre Completo del Supervisor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ingresa el nombre completo del supervisor"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="supervisor@ejemplo.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-1"></i>Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            <i class="fas fa-lock me-1"></i>Confirmar Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required>
                                    </div>
                                </div>

                                <!-- Campo oculto para el colegio -->
                                <input type="hidden" name="colegio_id" value="{{ $adminColegio->id }}">

                                <!-- Información de permisos -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-shield-alt me-2"></i>Permisos del Supervisor
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>Ver denuncias de {{ $adminColegio->nombre }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Agregar seguimiento a las denuncias</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Cambiar estado de denuncias</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Cerrar denuncias cuando esté listo</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus"></i> Crear Supervisor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-danger">No puedes crear supervisores</h5>
                            <p class="text-muted">
                                Tu cuenta de administrador no está asociada a un colegio. 
                                Para crear supervisores, necesitas tener un colegio asignado.
                            </p>
                            <p class="text-muted">
                                Por favor, contacta a un <strong>Super Administrador</strong> para que asocie tu cuenta a un colegio.
                            </p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>