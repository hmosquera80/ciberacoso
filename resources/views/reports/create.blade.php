<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Denuncia - "Cuéntanos lo que te pasa"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-body {
            padding: 40px;
        }
        .section-title {
            color: #667eea;
            border-bottom: 2px solid #f093fb;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-size: 1.3em;
            font-weight: 600;
        }
        .form-check {
            margin-bottom: 12px;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        .btn-submit {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            color: white;
        }
        .alert-info {
            background: linear-gradient(45deg, #667eea20, #764ba220);
            border: 1px solid #667eea50;
            border-radius: 10px;
        }
        .required {
            color: #dc3545;
        }
        .form-control.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.7-.7 1.4-1.4.7-.7L5.6 3.3 4.2 1.9a.5.5 0 0 0-.7 0L1.6 3.8a.5.5 0 0 0 0 .7l.7.73z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 4.4 4.8m0-4.8-4.4 4.8'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            cursor: not-allowed;
        }
        .validation-message {
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .validation-message.valid {
            color: #28a745;
        }
        .validation-message.invalid {
            color: #dc3545;
        }
        .municipio-info {
            background: linear-gradient(45deg, #28a74520, #20c99720);
            border: 1px solid #28a74550;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.9em;
        }
        .loading-colegios {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px;
            text-align: center;
            margin-top: 5px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>🧑‍💻 Formulario de Denuncia</h1>
                <h3>"Cuéntanos lo que te pasa"</h3>
            </div>
            
            <div class="form-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('report.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="section-title">🧑‍💻 Datos iniciales</div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre completo del denunciante <span class="required">*</span></label>
                            <input type="text" class="form-control" name="denunciante_nombre_completo" value="{{ old('denunciante_nombre_completo') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de nacimiento <span class="required">*</span></label>
                            <input type="date" class="form-control" name="denunciante_fecha_nacimiento" id="fecha_nacimiento" value="{{ old('denunciante_fecha_nacimiento') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Edad <span class="text-muted">(calculada automáticamente)</span></label>
                            <input type="number" class="form-control" name="denunciante_edad" id="edad" value="{{ old('denunciante_edad') }}" readonly style="background-color: #f8f9fa;">
                            <div id="mensaje-menor-edad"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio <span class="required">*</span></label>
                            <select class="form-control" name="denunciante_municipio_id" id="denunciante_municipio_id" required>
                                <option value="">Selecciona un municipio</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" {{ old('denunciante_municipio_id') == $municipio->id ? 'selected' : '' }}>
                                        {{ $municipio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="municipio-validation-message" class="validation-message"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del colegio <span class="required">*</span></label>
                            <select class="form-control" name="denunciante_colegio_id" id="denunciante_colegio_id" required disabled>
                                <option value="">Primero selecciona un municipio</option>
                            </select>
                            <div id="loading-colegios" class="loading-colegios" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Cargando colegios...
                            </div>
                            <div id="colegio-validation-message" class="validation-message"></div>
                            <div id="municipio-info" class="municipio-info" style="display: none;">
                                <i class="fas fa-info-circle"></i> <strong>Colegios disponibles:</strong> <span id="colegios-count">0</span> instituciones en este municipio.
                            </div>
                        </div>
                    </div>

                    <!-- GUARDAMOS TODOS LOS COLEGIOS EN UN SCRIPT HIDDEN PARA USO DEL JAVASCRIPT -->
                    <script type="application/json" id="colegios-data">
                        @json($colegios)
                    </script>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Curso / grado <span class="required">*</span></label>
                            <input type="text" class="form-control" name="denunciante_curso_grado" value="{{ old('denunciante_curso_grado') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"># de Identificación <span class="required">*</span></label>
                            <input type="text" class="form-control" name="denunciante_identificacion" value="{{ old('denunciante_identificacion') }}" required>
                        </div>
                    </div>

                    <div class="section-title mt-5">¿Cuál fue la red social donde ocurrieron los hechos? <span class="required">*</span></div>
                    <div class="row">
                        @foreach($socialMediaOptions as $option)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="red_social[]" value="{{ $option->id }}" id="social_media_{{ $option->id }}"
                                           {{ in_array($option->id, old('red_social', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="social_media_{{ $option->id }}">{{ $option->name }}</label>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12 mt-3" id="otro_red_social_div" style="{{ old('otro_red_social') ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label">Otro (especifica):</label>
                            <input type="text" class="form-control" name="otro_red_social" value="{{ old('otro_red_social') }}" placeholder="Especifica otra red social...">
                        </div>
                    </div>

                    <div class="section-title mt-5">¿Qué está pasando? <span class="required">*</span></div>
                    <div class="row">
                        <div class="col-12">
                            @foreach($bullyingTypeOptions as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="que_esta_pasando[]" value="{{ $option->id }}" id="bullying_type_{{ $option->id }}"
                                           {{ in_array($option->id, old('que_esta_pasando', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bullying_type_{{ $option->id }}">{{ $option->description }}</label>
                                </div>
                            @endforeach
                            <div class="mt-3" id="otro_que_esta_pasando_div" style="{{ old('otro_que_esta_pasando') ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">Otra cosa (escríbela):</label>
                                <textarea class="form-control" name="otro_que_esta_pasando" rows="2" placeholder="Describe otra situación...">{{ old('otro_que_esta_pasando') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="section-title mt-5">¿La persona afectada eres tú o alguien más? <span class="required">*</span></div>
                    @foreach(['Soy yo', 'Es otra persona', 'Prefiero no decir', 'Otra persona y yo'] as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="afectado_quien" value="{{ $option }}" id="afectado_{{ Str::slug($option) }}"
                                   {{ old('afectado_quien') == $option ? 'checked' : '' }} required>
                            <label class="form-check-label" for="afectado_{{ Str::slug($option) }}">{{ $option }}</label>
                        </div>
                    @endforeach

                    <div class="section-title mt-5">¿Conoces al agresor? <span class="required">*</span></div>
                    @foreach(['si', 'no', 'sospecho quien es'] as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="agresor_conocido" value="{{ $option }}" id="agresor_{{ Str::slug($option) }}"
                                   {{ old('agresor_conocido') == $option ? 'checked' : '' }} required>
                            <label class="form-check-label" for="agresor_{{ Str::slug($option) }}">
                                @if($option == 'si') Sí @elseif($option == 'no') No @else Sospecho quién es @endif
                            </label>
                        </div>
                    @endforeach
                    <div class="mt-3" id="agresor_nombre_div" style="{{ (old('agresor_conocido') == 'si' || old('agresor_conocido') == 'sospecho quien es') ? '' : 'display:none;' }}">
                        <label class="form-label">Nombre (si aplica):</label>
                        <input type="text" class="form-control" name="agresor_nombre" value="{{ old('agresor_nombre') }}" placeholder="Nombre del agresor...">
                    </div>

                    <div class="section-title mt-5">¿Hace cuánto está pasando?</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Días</label>
                            <input type="number" class="form-control" name="tiempo_dias" id="tiempo_dias" value="{{ old('tiempo_dias', 0) }}" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Meses</label>
                            <input type="number" class="form-control" name="tiempo_meses" id="tiempo_meses" value="{{ old('tiempo_meses', 0) }}" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Años</label>
                            <input type="number" class="form-control" name="tiempo_anios" id="tiempo_anios" value="{{ old('tiempo_anios', 0) }}" min="0">
                        </div>
                    </div>

                    <div class="section-title mt-5">¿Cómo te sientes por esto? <span class="required">*</span></div>
                    @foreach($feelingOptions as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="como_te_sientes[]" value="{{ $option->id }}" id="feeling_{{ $option->id }}"
                                   {{ in_array($option->id, old('como_te_sientes', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="feeling_{{ $option->id }}">{{ $option->description }}</label>
                        </div>
                    @endforeach

                    <div class="section-title mt-5">¿Ya reportaste esto por otro medio? <span class="required">*</span></div>
                    @foreach($reportChannelOptions as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reportado_otro_medio" value="{{ $option->name }}" id="report_channel_{{ Str::slug($option->name) }}"
                                   {{ old('reportado_otro_medio') == $option->name ? 'checked' : '' }} required>
                            <label class="form-check-label" for="report_channel_{{ Str::slug($option->name) }}">{{ $option->name }}</label>
                        </div>
                    @endforeach
                    <div class="mt-3" id="reportado_cual_linea_div" style="{{ old('reportado_otro_medio') == 'Sí, llamé a la línea telefónica' ? '' : 'display:none;' }}">
                        <label class="form-label">¿Cuál línea telefónica? (si aplica):</label>
                        <input type="text" class="form-control" name="reportado_cual_linea" value="{{ old('reportado_cual_linea') }}" placeholder="Especifica la línea telefónica...">
                    </div>

                    <div class="section-title mt-5">Realiza un breve resumen de los hechos <span class="required">*</span></div>
                    <textarea class="form-control" name="resumen_hechos" rows="5" required placeholder="Describe detalladamente lo que ha ocurrido...">{{ old('resumen_hechos') }}</textarea>

                    <div class="section-title mt-5">¿Deseas que alguien te contacte o hable contigo? <span class="required">*</span></div>
                    @foreach(['Sí, quiero que me llamen o escriban', 'No por ahora, solo quería contar lo que me pasa', 'Me gustaría recibir ayuda después'] as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contacto_deseado" value="{{ $option }}" id="contacto_{{ Str::slug($option) }}"
                                   {{ old('contacto_deseado') == $option ? 'checked' : '' }} required>
                            <label class="form-check-label" for="contacto_{{ Str::slug($option) }}">{{ $option }}</label>
                        </div>
                    @endforeach

                    <div class="section-title mt-5">¿TIENES PRUEBAS?</div>
                    <div class="mb-3">
                        <label class="form-label">Anexar evidencia (imágenes, documentos, etc.)</label>
                        <input type="file" class="form-control" name="evidencia_file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.mp4,.mov">
                        <div class="form-text">Puedes subir un archivo. Formatos permitidos: JPG, PNG, PDF, DOC, DOCX, MP4, MOV. Máximo 20MB.</div>
                    </div>

                    <div class="alert alert-info mt-5">
                        <h5>🔐 Aviso importante:</h5>
                        <p class="mb-0">Tu denuncia es <strong>confidencial</strong>. No se compartirá sin tu permiso. Si es urgente o hay riesgo para ti o alguien más, podemos hablar con un adulto responsable o especialista para ayudarte.</p>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit btn-lg" id="submit-btn">
                            📤 Enviar Denuncia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================================================
        // VARIABLES GLOBALES Y CONFIGURACIÓN
        // ============================================================================
        let formSubmitted = false;
        let todosLosColegios = [];

        // Cargar datos de colegios desde el JSON embebido
        try {
            const colegiosDataElement = document.getElementById('colegios-data');
            if (colegiosDataElement) {
                todosLosColegios = JSON.parse(colegiosDataElement.textContent);
                console.log('Colegios cargados:', todosLosColegios.length);
            }
        } catch (error) {
            console.error('Error al cargar datos de colegios:', error);
            todosLosColegios = [];
        }

        // Obtener IDs de opciones especiales desde PHP
        const otroSocialMediaId = {{ $socialMediaOptions->firstWhere('name', 'Otro')->id ?? 'null' }};
        const otraCosaBullyingTypeId = {{ $bullyingTypeOptions->firstWhere('description', 'like', '%Otra cosa%')->id ?? 'null' }};

        // ============================================================================
        // FUNCIÓN PARA CALCULAR EDAD
        // ============================================================================
        function calcularEdad(fechaNacimiento) {
            const hoy = new Date();
            const nacimiento = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            
            return edad;
        }

        // ============================================================================
        // FUNCIÓN PRINCIPAL: FILTRAR COLEGIOS POR MUNICIPIO
        // ============================================================================
        function filtrarColegiosPorMunicipio(municipioId) {
            const colegioSelect = document.getElementById('denunciante_colegio_id');
            const loadingDiv = document.getElementById('loading-colegios');
            const municipioInfo = document.getElementById('municipio-info');
            const colegiosCount = document.getElementById('colegios-count');
            const municipioValidationMessage = document.getElementById('municipio-validation-message');
            const colegioValidationMessage = document.getElementById('colegio-validation-message');

            console.log('Filtrando colegios para municipio ID:', municipioId);
            console.log('Total de colegios disponibles:', todosLosColegios.length);

            // Limpiar validaciones previas
            colegioSelect.classList.remove('is-valid', 'is-invalid');
            colegioValidationMessage.textContent = '';

            // Si no hay municipio seleccionado
            if (!municipioId || municipioId === '') {
                console.log('No hay municipio seleccionado');
                colegioSelect.disabled = true;
                colegioSelect.innerHTML = '<option value="">Primero selecciona un municipio</option>';
                municipioInfo.style.display = 'none';
                loadingDiv.style.display = 'none';
                
                // Validación de municipio
                const municipioSelect = document.getElementById('denunciante_municipio_id');
                municipioSelect.classList.add('is-invalid');
                municipioValidationMessage.textContent = 'Debes seleccionar un municipio primero.';
                municipioValidationMessage.className = 'validation-message invalid';
                return;
            }

            // Validación exitosa de municipio
            const municipioSelect = document.getElementById('denunciante_municipio_id');
            municipioSelect.classList.remove('is-invalid');
            municipioSelect.classList.add('is-valid');
            municipioValidationMessage.textContent = '✓ Municipio seleccionado correctamente.';
            municipioValidationMessage.className = 'validation-message valid';

            // Mostrar loading
            loadingDiv.style.display = 'block';
            colegioSelect.disabled = true;
            colegioSelect.innerHTML = '<option value="">Cargando colegios...</option>';

            // Simular pequeño delay para UX (opcional)
            setTimeout(() => {
                try {
                    // Filtrar colegios del municipio seleccionado
                    const colegiosFiltrados = todosLosColegios.filter(colegio => {
                        const perteneceAlMunicipio = String(colegio.municipio_id) === String(municipioId);
                        console.log(`Colegio ${colegio.nombre}: municipio_id=${colegio.municipio_id}, coincide=${perteneceAlMunicipio}`);
                        return perteneceAlMunicipio;
                    });

                    console.log('Colegios filtrados:', colegiosFiltrados.length);

                    // Limpiar el select
                    colegioSelect.innerHTML = '';
                    
                    // Agregar opción por defecto
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = colegiosFiltrados.length > 0 ? 'Selecciona un colegio' : 'No hay colegios disponibles';
                    colegioSelect.appendChild(defaultOption);

                    // Agregar colegios filtrados
                    if (colegiosFiltrados.length > 0) {
                        colegiosFiltrados.forEach(colegio => {
                            const option = document.createElement('option');
                            option.value = colegio.id;
                            option.textContent = colegio.nombre;
                            
                            // Restaurar selección previa si existe
                            const valorAnterior = "{{ old('denunciante_colegio_id') }}";
                            if (valorAnterior && String(colegio.id) === String(valorAnterior)) {
                                option.selected = true;
                                console.log('Restaurando selección previa:', colegio.nombre);
                            }
                            
                            colegioSelect.appendChild(option);
                        });

                        // Habilitar el select
                        colegioSelect.disabled = false;

                        // Mostrar información del municipio
                        colegiosCount.textContent = colegiosFiltrados.length;
                        municipioInfo.style.display = 'block';

                        // Validar selección de colegio si hay uno seleccionado
                        if (colegioSelect.value !== '') {
                            colegioSelect.classList.add('is-valid');
                            colegioValidationMessage.textContent = '✓ Colegio seleccionado correctamente.';
                            colegioValidationMessage.className = 'validation-message valid';
                        }

                    } else {
                        // No hay colegios para este municipio
                        colegioSelect.disabled = true;
                        municipioInfo.style.display = 'none';
                        
                        // Mostrar mensaje de error
                        colegioSelect.classList.add('is-invalid');
                        colegioValidationMessage.textContent = 'No hay colegios registrados para este municipio.';
                        colegioValidationMessage.className = 'validation-message invalid';
                    }

                } catch (error) {
                    console.error('Error al filtrar colegios:', error);
                    colegioSelect.innerHTML = '<option value="">Error al cargar colegios</option>';
                    colegioSelect.disabled = true;
                    
                    // Mostrar mensaje de error
                    colegioValidationMessage.textContent = 'Error al cargar colegios. Recarga la página.';
                    colegioValidationMessage.className = 'validation-message invalid';
                }

                // Ocultar loading
                loadingDiv.style.display = 'none';
            }, 300); // 300ms de delay para suavizar la experiencia
        }

        // ============================================================================
        // EVENT LISTENERS PRINCIPALES
        // ============================================================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado, inicializando formulario...');

            // Event listener para cambio de municipio
            const municipioSelect = document.getElementById('denunciante_municipio_id');
            if (municipioSelect) {
                municipioSelect.addEventListener('change', function() {
                    const municipioId = this.value;
                    console.log('Municipio cambiado a:', municipioId);
                    filtrarColegiosPorMunicipio(municipioId);
                });
            }

            // Event listener para validación de colegio
            const colegioSelect = document.getElementById('denunciante_colegio_id');
            if (colegioSelect) {
                colegioSelect.addEventListener('change', function() {
                    const colegioValidationMessage = document.getElementById('colegio-validation-message');
                    
                    if (this.disabled) {
                        this.classList.remove('is-valid', 'is-invalid');
                        colegioValidationMessage.textContent = '';
                    } else if (this.value === '') {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                        colegioValidationMessage.textContent = 'Debes seleccionar un colegio.';
                        colegioValidationMessage.className = 'validation-message invalid';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        colegioValidationMessage.textContent = '✓ Colegio seleccionado correctamente.';
                        colegioValidationMessage.className = 'validation-message valid';
                    }
                });
            }

            // Inicializar filtrado si hay municipio seleccionado previamente
            const municipioSeleccionado = municipioSelect.value;
            if (municipioSeleccionado) {
                console.log('Inicializando con municipio preseleccionado:', municipioSeleccionado);
                filtrarColegiosPorMunicipio(municipioSeleccionado);
            }
        });

        // ============================================================================
        // CÁLCULO AUTOMÁTICO DE EDAD
        // ============================================================================
        document.getElementById('fecha_nacimiento').addEventListener('change', function() {
            const fechaNacimiento = this.value;
            const edadInput = document.getElementById('edad');
            const mensajeEdadDiv = document.getElementById('mensaje-menor-edad');

            if (fechaNacimiento) {
                const edad = calcularEdad(fechaNacimiento);
                edadInput.value = edad;
                
                if (edad < 18) {
                    mensajeEdadDiv.innerHTML = `
                        <div class="alert alert-info mt-2">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                <strong>Menor de edad detectado:</strong> 
                                Tu denuncia recibirá atención prioritaria y se contactará con un adulto responsable si es necesario.
                            </small>
                        </div>`;
                } else {
                    mensajeEdadDiv.innerHTML = '';
                }
            } else {
                edadInput.value = '';
                mensajeEdadDiv.innerHTML = '';
            }
        });

        // Calcular edad al cargar la página si ya hay una fecha
        window.addEventListener('load', function() {
            const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
            if (fechaNacimiento) {
                document.getElementById('fecha_nacimiento').dispatchEvent(new Event('change'));
            }
        });

        // ============================================================================
        // CAMPOS CONDICIONALES - REDES SOCIALES
        // ============================================================================
        const socialMediaCheckboxes = document.querySelectorAll('input[name="red_social[]"]');
        const otroRedSocialDiv = document.getElementById('otro_red_social_div');
        const otroRedSocialInput = otroRedSocialDiv?.querySelector('input[name="otro_red_social"]');

        socialMediaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (otroSocialMediaId && this.value == otroSocialMediaId) {
                    if (this.checked) {
                        otroRedSocialDiv.style.display = 'block';
                        otroRedSocialInput.focus();
                    } else {
                        otroRedSocialDiv.style.display = 'none';
                        otroRedSocialInput.value = '';
                    }
                }
            });
        });

        // Inicializar estado de "Otro" para redes sociales
        window.addEventListener('load', function() {
            const oldRedSocials = @json(old('red_social', []));
            if (otroSocialMediaId && oldRedSocials.includes(String(otroSocialMediaId))) {
                if (otroRedSocialDiv) otroRedSocialDiv.style.display = 'block';
            }
        });

        // ============================================================================
        // CAMPOS CONDICIONALES - TIPOS DE ACOSO
        // ============================================================================
        const bullyingTypeCheckboxes = document.querySelectorAll('input[name="que_esta_pasando[]"]');
        const otroQueEstaPasandoDiv = document.getElementById('otro_que_esta_pasando_div');
        const otroQueEstaPasandoTextarea = otroQueEstaPasandoDiv?.querySelector('textarea[name="otro_que_esta_pasando"]');

        bullyingTypeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (otraCosaBullyingTypeId && this.value == otraCosaBullyingTypeId) {
                    if (this.checked) {
                        otroQueEstaPasandoDiv.style.display = 'block';
                        otroQueEstaPasandoTextarea.focus();
                    } else {
                        otroQueEstaPasandoDiv.style.display = 'none';
                        otroQueEstaPasandoTextarea.value = '';
                    }
                }
            });
        });

        // Inicializar estado de "Otra cosa" para tipos de acoso
        window.addEventListener('load', function() {
            const oldQueEstaPasando = @json(old('que_esta_pasando', []));
            if (otraCosaBullyingTypeId && oldQueEstaPasando.includes(String(otraCosaBullyingTypeId))) {
                if (otroQueEstaPasandoDiv) otroQueEstaPasandoDiv.style.display = 'block';
            }
        });

        // ============================================================================
        // CAMPOS CONDICIONALES - NOMBRE DEL AGRESOR
        // ============================================================================
        document.querySelectorAll('input[name="agresor_conocido"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const nombreAgresorDiv = document.getElementById('agresor_nombre_div');
                const nombreAgresorInput = nombreAgresorDiv?.querySelector('input[name="agresor_nombre"]');
                
                if (this.value === 'si' || this.value === 'sospecho quien es') {
                    nombreAgresorDiv.style.display = 'block';
                    setTimeout(() => nombreAgresorInput?.focus(), 100);
                } else {
                    nombreAgresorDiv.style.display = 'none';
                    if (nombreAgresorInput) nombreAgresorInput.value = '';
                }
            });
        });

        // Inicializar estado del nombre del agresor
        window.addEventListener('load', function() {
            const selectedAgresorConocido = document.querySelector('input[name="agresor_conocido"]:checked');
            const nombreAgresorDiv = document.getElementById('agresor_nombre_div');
            
            if (selectedAgresorConocido && (selectedAgresorConocido.value === 'si' || selectedAgresorConocido.value === 'sospecho quien es')) {
                nombreAgresorDiv.style.display = 'block';
            } else {
                nombreAgresorDiv.style.display = 'none';
            }
        });

        // ============================================================================
        // CAMPOS CONDICIONALES - LÍNEA TELEFÓNICA
        // ============================================================================
        document.querySelectorAll('input[name="reportado_otro_medio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const cualLineaDiv = document.getElementById('reportado_cual_linea_div');
                const cualLineaInput = cualLineaDiv?.querySelector('input[name="reportado_cual_linea"]');
                
                if (this.value === 'Sí, llamé a la línea telefónica') {
                    cualLineaDiv.style.display = 'block';
                    setTimeout(() => cualLineaInput?.focus(), 100);
                } else {
                    cualLineaDiv.style.display = 'none';
                    if (cualLineaInput) cualLineaInput.value = '';
                }
            });
        });

        // Inicializar estado de línea telefónica
        window.addEventListener('load', function() {
            const selectedReportadoOtroMedio = document.querySelector('input[name="reportado_otro_medio"]:checked');
            const cualLineaDiv = document.getElementById('reportado_cual_linea_div');
            
            if (selectedReportadoOtroMedio && selectedReportadoOtroMedio.value === 'Sí, llamé a la línea telefónica') {
                cualLineaDiv.style.display = 'block';
            } else {
                cualLineaDiv.style.display = 'none';
            }
        });

        // ============================================================================
        // VALIDACIÓN COMPLETA DEL FORMULARIO
        // ============================================================================
        function validarFormularioCompleto() {
            let errores = [];
            let primerCampoConError = null;

            // 1. Validar municipio
            const municipioSelect = document.getElementById('denunciante_municipio_id');
            if (!municipioSelect.value) {
                errores.push('• Debes seleccionar un municipio.');
                municipioSelect.classList.add('is-invalid');
                if (!primerCampoConError) primerCampoConError = municipioSelect;
            }

            // 2. Validar colegio
            const colegioSelect = document.getElementById('denunciante_colegio_id');
            if (colegioSelect.disabled || !colegioSelect.value) {
                errores.push('• Debes seleccionar un colegio válido para el municipio elegido.');
                if (!colegioSelect.disabled) {
                    colegioSelect.classList.add('is-invalid');
                    if (!primerCampoConError) primerCampoConError = colegioSelect;
                }
            }

            // 3. Validar redes sociales
            const redesSociales = document.querySelectorAll('input[name="red_social[]"]:checked');
            if (redesSociales.length === 0) {
                errores.push('• Por favor selecciona al menos una red social donde ocurrieron los hechos.');
                const primerCheckboxRedSocial = document.querySelector('input[name="red_social[]"]');
                if (!primerCampoConError && primerCheckboxRedSocial) primerCampoConError = primerCheckboxRedSocial;
            }

            // 4. Validar qué está pasando
            const queEstaPasando = document.querySelectorAll('input[name="que_esta_pasando[]"]:checked');
            if (queEstaPasando.length === 0) {
                errores.push('• Por favor selecciona al menos una opción de lo que está pasando.');
                const primerCheckboxBullying = document.querySelector('input[name="que_esta_pasando[]"]');
                if (!primerCampoConError && primerCheckboxBullying) primerCampoConError = primerCheckboxBullying;
            }

            // 5. Validar sentimientos
            const comoSeSiente = document.querySelectorAll('input[name="como_te_sientes[]"]:checked');
            if (comoSeSiente.length === 0) {
                errores.push('• Por favor selecciona al menos una opción de cómo te sientes.');
                const primerCheckboxSentimiento = document.querySelector('input[name="como_te_sientes[]"]');
                if (!primerCampoConError && primerCheckboxSentimiento) primerCampoConError = primerCheckboxSentimiento;
            }

            // 6. Validar edad
            const edad = parseInt(document.getElementById('edad').value);
            if (isNaN(edad) || edad < 0 || edad > 120) {
                errores.push('• Por favor verifica que la fecha de nacimiento sea correcta.');
                const fechaNacimiento = document.getElementById('fecha_nacimiento');
                if (!primerCampoConError && fechaNacimiento) primerCampoConError = fechaNacimiento;
            }

            // 7. Validar campos de tiempo (al menos uno > 0)
            const tiempoDias = parseInt(document.getElementById('tiempo_dias').value) || 0;
            const tiempoMeses = parseInt(document.getElementById('tiempo_meses').value) || 0;
            const tiempoAnios = parseInt(document.getElementById('tiempo_anios').value) || 0;

            if (tiempoDias === 0 && tiempoMeses === 0 && tiempoAnios === 0) {
                errores.push('• Debes especificar un tiempo transcurrido (días, meses o años) que sea mayor a cero.');
                const tiempoDiasInput = document.getElementById('tiempo_dias');
                if (!primerCampoConError && tiempoDiasInput) primerCampoConError = tiempoDiasInput;
            }

            // 8. Validar campos requeridos básicos
            const camposRequeridos = [
                { elemento: document.querySelector('input[name="denunciante_nombre_completo"]'), mensaje: '• El nombre completo es requerido.' },
                { elemento: document.querySelector('input[name="denunciante_fecha_nacimiento"]'), mensaje: '• La fecha de nacimiento es requerida.' },
                { elemento: document.querySelector('input[name="denunciante_curso_grado"]'), mensaje: '• El curso/grado es requerido.' },
                { elemento: document.querySelector('input[name="denunciante_identificacion"]'), mensaje: '• El número de identificación es requerido.' },
                { elemento: document.querySelector('textarea[name="resumen_hechos"]'), mensaje: '• El resumen de los hechos es requerido.' }
            ];

            camposRequeridos.forEach(campo => {
                if (campo.elemento && !campo.elemento.value.trim()) {
                    errores.push(campo.mensaje);
                    campo.elemento.classList.add('is-invalid');
                    if (!primerCampoConError) primerCampoConError = campo.elemento;
                }
            });

            // 9. Validar campos de radio requeridos
            const camposRadioRequeridos = [
                { name: 'afectado_quien', mensaje: '• Debes especificar quién es la persona afectada.' },
                { name: 'agresor_conocido', mensaje: '• Debes especificar si conoces al agresor.' },
                { name: 'reportado_otro_medio', mensaje: '• Debes especificar si ya reportaste por otro medio.' },
                { name: 'contacto_deseado', mensaje: '• Debes especificar si deseas que te contacten.' }
            ];

            camposRadioRequeridos.forEach(campo => {
                const seleccionado = document.querySelector(`input[name="${campo.name}"]:checked`);
                if (!seleccionado) {
                    errores.push(campo.mensaje);
                    const primerRadio = document.querySelector(`input[name="${campo.name}"]`);
                    if (!primerCampoConError && primerRadio) primerCampoConError = primerRadio;
                }
            });

            return { errores, primerCampoConError };
        }

        // ============================================================================
        // MANEJO DEL ENVÍO DEL FORMULARIO
        // ============================================================================
        document.querySelector('form').addEventListener('submit', function(e) {
            // Prevenir envío múltiple
            if (formSubmitted) {
                e.preventDefault();
                console.log('Formulario ya enviado, previniendo envío múltiple');
                return false;
            }

            console.log('Validando formulario antes del envío...');
            const validacion = validarFormularioCompleto();

            // Si hay errores, prevenir el envío
            if (validacion.errores.length > 0) {
                e.preventDefault();
                console.log('Errores encontrados:', validacion.errores);
                
                // Mostrar errores al usuario
                const mensajeError = '¡Ups! Parece que faltan algunos datos o hay errores:\n\n' + 
                                   validacion.errores.join('\n') + 
                                   '\n\nPor favor revisa y completa la información requerida.';
                alert(mensajeError);
                
                // Hacer scroll al primer campo con error
                if (validacion.primerCampoConError) {
                    validacion.primerCampoConError.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    setTimeout(() => validacion.primerCampoConError.focus(), 500);
                }
                
                return false;
            }

            // Validación final: Confirmar envío
            const municipioSelect = document.getElementById('denunciante_municipio_id');
            const colegioSelect = document.getElementById('denunciante_colegio_id');
            const edad = document.getElementById('edad').value;

            const confirmMessage = `¿Estás seguro de que quieres enviar esta denuncia?

INFORMACIÓN A ENVIAR:
• Municipio: ${municipioSelect.options[municipioSelect.selectedIndex].text}
• Colegio: ${colegioSelect.options[colegioSelect.selectedIndex].text}
• Edad: ${edad} años

Una vez enviada, será procesada de manera confidencial y segura.

¿Continuar con el envío?`;

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                console.log('Usuario canceló el envío');
                return false;
            }

            // Deshabilitar botón de envío y marcar como enviado
            console.log('Enviando formulario...');
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            }
            
            formSubmitted = true;
            return true;
        });

        // ============================================================================
        // DEBUGGING Y LOGGING
        // ============================================================================
        console.log('Script inicializado correctamente');
        console.log('Configuración:', {
            otroSocialMediaId,
            otraCosaBullyingTypeId,
            totalColegios: todosLosColegios.length
        });
    </script>
</body>
</html>