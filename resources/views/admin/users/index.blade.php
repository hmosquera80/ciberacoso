<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Panel Administrativo</title>
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
        .role-badge {
            font-size: 0.75em;
            padding: 0.4em 0.6em;
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
                        <h1><i class="fas fa-users me-2"></i>Gestión de Usuarios</h1>
                        <p class="text-muted">Administra los usuarios del sistema</p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Usuario
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
                        <h5><i class="fas fa-list me-2"></i>Lista de Usuarios</h5>
                    </div>
                    <div class="card-body">
                        @if ($users->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay usuarios registrados</h5>
                                <p class="text-muted">Comienza agregando el primer usuario al sistema.</p>
                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Usuario
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Colegio</th>
                                            <th>Municipio</th>
                                            <th>Fecha de Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td><strong>#{{ $user->id }}</strong></td>
                                                <td>
                                                    <i class="fas fa-user me-2 text-primary"></i>
                                                    {{ $user->name }}
                                                </td>
                                                <td>
                                                    <i class="fas fa-envelope me-2 text-secondary"></i>
                                                    {{ $user->email }}
                                                </td>
                                                <td>
                                                    @php
                                                        $roleBadgeClass = '';
                                                        $roleIcon = '';
                                                        switch ($user->role) {
                                                            case 'super_admin':
                                                                $roleBadgeClass = 'bg-danger';
                                                                $roleIcon = 'fas fa-crown';
                                                                $roleText = 'Super Admin';
                                                                break;
                                                            case 'admin':
                                                                $roleBadgeClass = 'bg-warning';
                                                                $roleIcon = 'fas fa-user-tie';
                                                                $roleText = 'Admin';
                                                                break;
                                                            case 'supervisor':
                                                                $roleBadgeClass = 'bg-info';
                                                                $roleIcon = 'fas fa-user-check';
                                                                $roleText = 'Supervisor';
                                                                break;
                                                            default:
                                                                $roleBadgeClass = 'bg-secondary';
                                                                $roleIcon = 'fas fa-user';
                                                                $roleText = 'Usuario';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $roleBadgeClass }} role-badge">
                                                        <i class="{{ $roleIcon }}"></i> {{ $roleText }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($user->colegio)
                                                        <i class="fas fa-school me-2 text-success"></i>
                                                        {{ $user->colegio->nombre }}
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus"></i> Sin asignar
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->colegio && $user->colegio->municipio)
                                                        <i class="fas fa-map-marker-alt me-2 text-warning"></i>
                                                        {{ $user->colegio->municipio->nombre }}
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus"></i> N/A
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('users.edit', $user->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($user->id !== Auth::id())
                                                            <form action="{{ route('users.destroy', $user->id) }}" 
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary" disabled title="No puedes eliminarte a ti mismo">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $users->links() }}
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