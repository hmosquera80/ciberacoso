<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario - Panel Administrativo</title>
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
                        <h1><i class="fas fa-plus me-2"></i>Nuevo Usuario</h1>
                        <p class="text-muted">Agregar un nuevo usuario al sistema</p>
                    </div>
                    <div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                        <h5><i class="fas fa-user me-2"></i>Información del Usuario</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ingresa el nombre completo"
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
                                       placeholder="usuario@ejemplo.com"
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

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag me-1"></i>Rol <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Seleccione un rol</option>
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                                        <i class="fas fa-crown"></i> Super Administrador
                                    </option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        <i class="fas fa-user-tie"></i> Administrador
                                    </option>
                                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>
                                        <i class="fas fa-user-check"></i> Supervisor
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> El rol determina los permisos del usuario en el sistema
                                </div>
                            </div>

                            <div class="mb-3" id="colegio_assignment_div" style="{{ old('role') == 'admin' || old('role') == 'supervisor' ? 'display:block;' : 'display:none;' }}">
                                <label for="colegio_id" class="form-label">
                                    <i class="fas fa-school me-1"></i>Colegio Asignado
                                </label>
                                <select class="form-control @error('colegio_id') is-invalid @enderror" 
                                        id="colegio_id" 
                                        name="colegio_id">
                                    <option value="">Selecciona un colegio</option>
                                    @foreach($colegios as $colegio)
                                        <option value="{{ $colegio->id }}" {{ old('colegio_id') == $colegio->id ? 'selected' : '' }}>
                                            {{ $colegio->nombre }} 
                                            @if($colegio->municipio)
                                                ({{ $colegio->municipio->nombre }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('colegio_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Los roles 'Administrador' y 'Supervisor' deben tener un colegio asignado
                                </div>
                            </div>

                            @if($colegios->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>¡Atención!</strong> No hay colegios disponibles. 
                                    <a href="{{ route('colegios.create') }}" class="alert-link">Crear colegio primero</a>.
                                </div>
                            @endif

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const colegioAssignmentDiv = document.getElementById('colegio_assignment_div');
            const colegioSelect = document.getElementById('colegio_id');

            function toggleColegioAssignment() {
                if (roleSelect.value === 'admin' || roleSelect.value === 'supervisor') {
                    colegioAssignmentDiv.style.display = 'block';
                    colegioSelect.setAttribute('required', 'required');
                } else {
                    colegioAssignmentDiv.style.display = 'none';
                    colegioSelect.removeAttribute('required');
                    colegioSelect.value = '';
                }
            }

            roleSelect.addEventListener('change', toggleColegioAssignment);
            toggleColegioAssignment(); // Ejecutar al cargar la página
        });
    </script>
</body>
</html>