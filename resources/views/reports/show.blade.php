<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Denuncia #{{ $report->id }} - Ciberacoso</title>
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
            margin-bottom: 1.5rem; /* Espaciado entre tarjetas */
        }
        .card-header {
            background-color: #667eea; /* Color de cabecera similar al formulario */
            color: white;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            font-weight: bold;
        }
        .info-item {
            margin-bottom: 0.5rem;
        }
        .info-item strong {
            color: #34495e;
            display: inline-block;
            width: 150px; /* Ancho fijo para alinear etiquetas */
        }
        .badge {
            font-size: 0.9em;
            padding: 0.6em 1em;
        }
        .btn-action {
            margin-right: 0.5rem;
        }
        .form-action-section {
            background-color: #e9ecef;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
            left: 20px;
            margin-left: -1px;
        }
        .timeline > li {
            position: relative;
            min-height: 50px;
            margin-bottom: 15px;
        }
        .timeline > li:before,
        .timeline > li:after {
            content: " ";
            display: table;
        }
        .timeline > li:after {
            clear: both;
        }
        .timeline > li .timeline-panel {
            width: calc(100% - 75px);
            float: right;
            border: 1px solid #d4d4d4;
            border-radius: 0.25rem;
            padding: 15px;
            position: relative;
            background-color: white;
        }
        .timeline > li .timeline-panel:before {
            border-left: 0 solid transparent;
            border-right: 14px solid #d4d4d4;
            border-top: 14px solid transparent;
            border-bottom: 14px solid transparent;
            left: -14px;
            top: 24px;
            content: " ";
            height: 0;
            position: absolute;
            width: 0;
        }
        .timeline > li .timeline-panel:after {
            border-left: 0 solid transparent;
            border-right: 13px solid #fff;
            border-top: 13px solid transparent;
            border-bottom: 13px solid transparent;
            left: -13px;
            top: 25px;
            content: " ";
            height: 0;
            position: absolute;
            width: 0;
        }
        .timeline > li .timeline-badge {
            color: #fff;
            width: 46px;
            height: 46px;
            line-height: 46px;
            font-size: 1.2em;
            text-align: center;
            position: absolute;
            top: 16px;
            left: 0px;
            margin-left: -3px;
            background-color: #999999;
            z-index: 100;
            border-radius: 50%;
        }
        .timeline-title {
            margin-top: 0;
            color: inherit;
        }
        .timeline-body > p,
        .timeline-body > ul {
            margin-bottom: 0;
        }
        .timeline-body > p + p {
            margin-top: 5px;
        }
        .timeline-date {
            font-size: 0.85em;
            color: #888;
            margin-top: 0.5rem;
            text-align: right;
        }
        /* Color de badge según estado */
        .badge-abierta { background-color: #dc3545; }
        .badge-en-tramite { background-color: #ffc107; }
        .badge-pendiente-cierre { background-color: #fd7e14; }
        .badge-cerrada { background-color: #198754; }
        .badge-anotacion { background-color: #6c757d; } /* Para anotaciones sin cambio de estado */

        /* Responsive */
        @media (max-width: 767.98px) {
            .timeline:before {
                left: 50%;
            }
            .timeline > li .timeline-panel {
                width: 100%;
                float: none;
            }
            .timeline > li .timeline-badge {
                left: 50%;
                margin-left: -23px; /* Centrar la insignia */
            }
            .timeline > li .timeline-panel:before,
            .timeline > li .timeline-panel:after {
                border-left: 14px solid transparent;
                border-right: 14px solid transparent;
                border-bottom: 14px solid #d4d4d4; /* Flecha hacia arriba */
                left: 50%;
                margin-left: -14px;
                top: -14px; /* Posicionar arriba */
            }
            .timeline > li .timeline-panel:after {
                border-bottom: 13px solid #fff;
                top: -13px;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-eye me-2"></i>Detalle de Denuncia #{{ $report->id }}</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver al Panel
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i> Información General
                <span class="badge float-end
                    @if($report->estado->nombre == 'Abierta') bg-danger
                    @elseif($report->estado->nombre == 'En Trámite') bg-warning
                    @elseif($report->estado->nombre == 'Pendiente de Cierre') bg-info
                    @elseif($report->estado->nombre == 'Cerrada') bg-success
                    @endif">
                    Estado Actual: {{ $report->estado->nombre }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item"><strong>Denunciante:</strong> {{ $report->denunciante_nombre_completo }} ({{ $report->denunciante_edad }} años)</div>
                        <div class="info-item"><strong>Identificación:</strong> {{ $report->denunciante_identificacion }}</div>
                        <div class="info-item"><strong>Municipio:</strong> {{ $report->denunciante_municipio }}</div>
                        <div class="info-item"><strong>Colegio:</strong> {{ $report->denunciante_colegio }}</div>
                        <div class="info-item"><strong>Curso/Grado:</strong> {{ $report->denunciante_curso_grado }}</div>
                        <div class="info-item"><strong>Fecha Denuncia:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item"><strong>Afectado:</strong> {{ $report->afectado_quien }}</div>
                        <div class="info-item"><strong>Agresor Conocido:</strong> {{ $report->agresor_conocido == 'si' ? 'Sí' : ($report->agresor_conocido == 'no' ? 'No' : 'Sospecha quién es') }}</div>
                        @if($report->agresor_nombre)
                            <div class="info-item"><strong>Nombre Agresor:</strong> {{ $report->agresor_nombre }}</div>
                        @endif
                        <div class="info-item"><strong>Tiempo Pasando:</strong>
                            @if ($report->tiempo_anios > 0) {{ $report->tiempo_anios }} año(s) @endif
                            @if ($report->tiempo_meses > 0) {{ $report->tiempo_meses }} mes(es) @endif
                            @if ($report->tiempo_dias > 0) {{ $report->tiempo_dias }} día(s) @endif
                            @if ($report->tiempo_anios == 0 && $report->tiempo_meses == 0 && $report->tiempo_dias == 0) No especificado @endif
                        </div>
                        <div class="info-item"><strong>Contacto Deseado:</strong> {{ $report->contacto_deseado }}</div>
                        <div class="info-item"><strong>Reportado Antes:</strong> {{ $report->reportado_otro_medio }}</div>
                        @if($report->reportado_cual_linea)
                            <div class="info-item"><strong>Línea Telefónica:</strong> {{ $report->reportado_cual_linea }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-bullhorn me-2"></i> Detalles del Acoso</div>
            <div class="card-body">
                <div class="info-item"><strong>Red(es) Social(es):</strong>
                    @if ($report->socialMedia->isEmpty()) N/A @else {{ $report->socialMedia->pluck('name')->join(', ') }} @endif
                </div>
                <div class="info-item"><strong>¿Qué está pasando?:</strong>
                    @if ($report->bullyingTypes->isEmpty()) N/A @else {{ $report->bullyingTypes->pluck('description')->join(', ') }} @endif
                </div>
                <div class="info-item"><strong>Cómo se siente:</strong>
                    @if ($report->feelings->isEmpty()) N/A @else {{ $report->feelings->pluck('description')->join(', ') }} @endif
                </div>
                <div class="info-item"><strong>Resumen de los Hechos:</strong>
                    <p class="mt-2 p-3 bg-light border rounded">{{ $report->resumen_hechos }}</p>
                </div>
                <div class="info-item"><strong>Tiene Pruebas:</strong> {{ $report->tiene_pruebas ? 'Sí' : 'No' }}</div>
                @if($report->evidencia_path)
                    <div class="info-item"><strong>Evidencia:</strong> <a href="{{ Storage::url($report->evidencia_path) }}" target="_blank"><i class="fas fa-file-alt me-1"></i> Ver Archivo</a></div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-history me-2"></i> Historial de Seguimiento</div>
            <div class="card-body">
                @if($report->seguimientos->isEmpty())
                    <p class="text-muted">No hay seguimiento registrado para esta denuncia.</p>
                @else
                    <ul class="timeline">
                        @foreach($report->seguimientos->sortBy('created_at') as $seguimiento)
                            @php
                                $badgeIcon = 'fas fa-pen';
                                $badgeClass = 'badge-anotacion'; // Default for general annotation

                                if ($seguimiento->denuncia_estado_anterior_id !== $seguimiento->denuncia_estado_nuevo_id) {
                                    // It was a status change
                                    switch ($seguimiento->estadoNuevo->nombre) {
                                        case 'Abierta': $badgeClass = 'bg-danger'; $badgeIcon = 'fas fa-folder-open'; break;
                                        case 'En Trámite': $badgeClass = 'bg-warning'; $badgeIcon = 'fas fa-clock'; break;
                                        case 'Pendiente de Cierre': $badgeClass = 'bg-info'; $badgeIcon = 'fas fa-hourglass-half'; break;
                                        case 'Cerrada': $badgeClass = 'bg-success'; $badgeIcon = 'fas fa-check-circle'; break;
                                    }
                                }
                            @endphp
                            <li>
                                <div class="timeline-badge {{ $badgeClass }}">
                                    <i class="{{ $badgeIcon }}"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">
                                            @if($seguimiento->denuncia_estado_anterior_id !== $seguimiento->denuncia_estado_nuevo_id)
                                                Cambio de estado:
                                                <span class="badge bg-secondary">{{ $seguimiento->estadoAnterior->nombre }}</span>
                                                <i class="fas fa-arrow-right"></i>
                                                <span class="badge {{ $badgeClass }}">{{ $seguimiento->estadoNuevo->nombre }}</span>
                                            @else
                                                Anotación:
                                            @endif
                                        </h4>
                                        <p><small class="text-muted"><i class="fas fa-user"></i> {{ $seguimiento->user->name }} - <i class="fas fa-calendar-alt"></i> {{ $seguimiento->created_at->format('d/m/Y H:i') }}</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>{{ $seguimiento->anotacion }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        @php
            $currentUser = Auth::user();
            $estadoActualNombre = $report->estado->nombre;
            $estadoAbiertaId = \App\Models\DenunciaEstado::where('nombre', 'Abierta')->first()->id;
            $estadoEnTramiteId = \App\Models\DenunciaEstado::where('nombre', 'En Trámite')->first()->id;
            $estadoPendienteCierreId = \App\Models\DenunciaEstado::where('nombre', 'Pendiente de Cierre')->first()->id;
            $estadoCerradaId = \App\Models\DenunciaEstado::where('nombre', 'Cerrada')->first()->id;

            $canChangeStatus = false;
            $canAddAnotacion = false; // Solo se puede añadir si no está cerrada
            $availableStates = collect();

            // Lógica para determinar qué acciones y estados están disponibles
            if ($currentUser->role === 'super_admin') {
                $canChangeStatus = true;
                $canAddAnotacion = ($estadoActualNombre !== 'Cerrada');
                $availableStates = \App\Models\DenunciaEstado::all();
            } elseif ($currentUser->role === 'admin' || $currentUser->role === 'supervisor') {
                if ($currentUser->colegio && $report->denunciante_colegio === $currentUser->colegio->nombre) {
                    $canAddAnotacion = ($estadoActualNombre !== 'Cerrada'); // Puede añadir anotación si no está cerrada

                    if ($estadoActualNombre === 'Abierta') {
                        $canChangeStatus = true;
                        $availableStates = \App\Models\DenunciaEstado::where('nombre', 'En Trámite')->get();
                    } elseif ($estadoActualNombre === 'En Trámite') {
                        if ($currentUser->role === 'admin' || $currentUser->role === 'supervisor') {
                            $canChangeStatus = true;
                            $availableStates = \App\Models\DenunciaEstado::where('nombre', 'Pendiente de Cierre')->get();
                        }
                    } elseif ($estadoActualNombre === 'Pendiente de Cierre' && $currentUser->role === 'supervisor') {
                        $canChangeStatus = true;
                        $availableStates = \App\Models\DenunciaEstado::where('nombre', 'Cerrada')->get();
                    }
                }
            }
        @endphp

        @if($canChangeStatus || $canAddAnotacion)
        <div class="card form-action-section">
            <div class="card-header"><i class="fas fa-cogs me-2"></i> Acciones de Gestión</div>
            <div class="card-body">
                <form action="{{ route('reports.updateStatus', $report) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="new_status_id" class="form-label">Cambiar Estado a:</label>
                        <select name="new_status_id" id="new_status_id" class="form-select" {{ !$canChangeStatus ? 'disabled' : '' }}>
                            <option value="">Selecciona un estado</option>
                            @foreach($availableStates as $estado)
                                <option value="{{ $estado->id }}" {{ old('new_status_id') == $estado->id ? 'selected' : '' }}
                                        @if($estado->id == $report->denuncia_estado_id) disabled @endif> {{-- No permitir seleccionar el estado actual --}}
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('new_status_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="anotacion_estado" class="form-label">Anotación (Obligatoria):</label>
                        <textarea name="anotacion_estado" id="anotacion_estado" rows="3" class="form-control" required {{ !$canAddAnotacion && !$canChangeStatus ? 'disabled' : '' }}>{{ old('anotacion_estado') }}</textarea>
                        @error('anotacion_estado')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary" {{ !$canAddAnotacion && !$canChangeStatus ? 'disabled' : '' }}>
                        <i class="fas fa-save me-2"></i> Guardar Cambios y Añadir Anotación
                    </button>
                    <small class="text-muted d-block mt-2">
                        Si no seleccionas un nuevo estado, solo se guardará la anotación.
                    </small>
                </form>
            </div>
        </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newStatusSelect = document.getElementById('new_status_id');
            const anotacionTextArea = document.getElementById('anotacion_estado');
            const submitButton = document.querySelector('.form-action-section .btn-primary');

            // Función para actualizar el estado del botón y campo de anotación
            function updateActionFormState() {
                const isStatusSelected = newStatusSelect.value !== '';
                const isAnotacionFilled = anotacionTextArea.value.trim().length > 0;

                // Habilitar el botón si se selecciona un estado O si se escribe una anotación
                if (isStatusSelected || isAnotacionFilled) {
                    submitButton.removeAttribute('disabled');
                    anotacionTextArea.setAttribute('required', 'required'); // La anotación es obligatoria si hay acción
                } else {
                    // Solo deshabilitar si no se puede cambiar estado Y la anotación está vacía
                    const canChangeStatusInitial = {{ json_encode($canChangeStatus) }};
                    const canAddAnotacionInitial = {{ json_encode($canAddAnotacion) }};

                    if (!canChangeStatusInitial && !canAddAnotacionInitial) {
                         submitButton.setAttribute('disabled', 'disabled');
                         anotacionTextArea.removeAttribute('required');
                    } else {
                         submitButton.setAttribute('disabled', 'disabled');
                         anotacionTextArea.removeAttribute('required'); // Al menos 10 chars, pero no requerido solo por existir
                    }
                }
            }

            // Escuchar cambios en el select de estado y en el textarea de anotación
            newStatusSelect.addEventListener('change', updateActionFormState);
            anotacionTextArea.addEventListener('input', updateActionFormState);

            // Llamar al cargar para establecer el estado inicial
            updateActionFormState();


            // Lógica para validar que si se selecciona un estado, la anotación no esté vacía (validación de front-end)
            document.querySelector('.form-action-section form').addEventListener('submit', function(e) {
                const selectedStatus = newStatusSelect.value;
                const anotacionText = anotacionTextArea.value.trim();

                if ((selectedStatus !== '' || anotacionText !== '') && anotacionText.length < 10) {
                    e.preventDefault();
                    alert('La anotación debe tener al menos 10 caracteres.');
                    anotacionTextArea.focus();
                }
            });
        });
    </script>
</body>
</html>