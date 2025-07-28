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
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del colegio <span class="required">*</span></label>
                            <select class="form-control" name="denunciante_colegio_id" id="denunciante_colegio_id" required>
                                <option value="">Selecciona un colegio</option>
                                @foreach($colegios as $colegio)
                                    <option value="{{ $colegio->id }}" 
                                            data-municipio-id="{{ $colegio->municipio_id }}"
                                            {{ old('denunciante_colegio_id') == $colegio->id ? 'selected' : '' }}>
                                        {{ $colegio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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
                            <input type="number" class="form-control" name="tiempo_dias" value="{{ old('tiempo_dias', 0) }}" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Meses</label>
                            <input type="number" class="form-control" name="tiempo_meses" value="{{ old('tiempo_meses', 0) }}" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Años</label>
                            <input type="number" class="form-control" name="tiempo_anios" value="{{ old('tiempo_anios', 0) }}" min="0">
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
                        <button type="submit" class="btn btn-submit btn-lg">
                            📤 Enviar Denuncia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para calcular la edad
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

        // Evento para calcular edad automáticamente y mostrar mensaje de menor de edad
        document.getElementById('fecha_nacimiento').addEventListener('change', function() {
            const fechaNacimiento = this.value;
            const edadInput = document.getElementById('edad');
            const mensajeEdadDiv = document.getElementById('mensaje-menor-edad');

            if (fechaNacimiento) {
                const edad = calcularEdad(fechaNacimiento);
                edadInput.value = edad;
                
                if (edad < 18) {
                    mensajeEdadDiv.innerHTML = '<div class="alert alert-info mt-2"><small><i class="fas fa-info-circle"></i> <strong>Menor de edad detectado:</strong> Tu denuncia recibirá atención prioritaria y se contactará con un adulto responsable si es necesario.</small></div>';
                } else {
                    mensajeEdadDiv.innerHTML = ''; // Limpiar mensaje si no es menor
                }
            } else {
                edadInput.value = '';
                mensajeEdadDiv.innerHTML = ''; // Limpiar mensaje si no hay fecha
            }
        });

        // Calcular edad al cargar la página si ya hay una fecha (para casos de validación fallida)
        window.addEventListener('load', function() {
            const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
            const edadInput = document.getElementById('edad');
            const mensajeEdadDiv = document.getElementById('mensaje-menor-edad');

            if (fechaNacimiento) {
                const edad = calcularEdad(fechaNacimiento);
                edadInput.value = edad;
                if (edad < 18) {
                    mensajeEdadDiv.innerHTML = '<div class="alert alert-info mt-2"><small><i class="fas fa-info-circle"></i> <strong>Menor de edad detectado:</strong> Tu denuncia recibirá atención prioritaria y se contactará con un adulto responsable si es necesario.</small></div>';
                }
            }
        });

        // Script para filtrar colegios por municipio
        document.addEventListener('DOMContentLoaded', function() {
            const municipioSelect = document.getElementById('denunciante_municipio_id');
            const colegioSelect = document.getElementById('denunciante_colegio_id');
            const allColegiosOptions = Array.from(colegioSelect.options); // Todas las opciones originales, incluyendo la de "Selecciona"

            function filterColegios() {
                const selectedMunicipioId = municipioSelect.value;
                let originalSelectedColegioId = colegioSelect.value; // Guardar el valor seleccionado actual

                // Limpiar el select de colegios, manteniendo la opción por defecto si no es un recarga de old()
                colegioSelect.innerHTML = '<option value="">Selecciona un colegio</option>';

                allColegiosOptions.forEach(option => {
                    // Ignorar la primera opción "Selecciona un colegio" del listado original si la hubiera
                    if (option.value === "") return; 

                    // Si no hay municipio seleccionado O si la opción de colegio pertenece al municipio seleccionado
                    if (selectedMunicipioId === "" || option.dataset.municipioId === selectedMunicipioId) {
                        colegioSelect.appendChild(option.cloneNode(true)); // Añadir una copia para no mover el original
                    }
                });

                // Intentar re-seleccionar el colegio que estaba antes, si es válido para el municipio actual
                if (selectedMunicipioId) { // Solo intentar si hay un municipio seleccionado
                    let foundOldColegio = false;
                    colegioSelect.querySelectorAll('option').forEach(option => {
                        if (option.value === originalSelectedColegioId) {
                            option.selected = true;
                            foundOldColegio = true;
                        }
                    });
                    // Si el colegio previamente seleccionado no está en la lista filtrada, resetear a la opción por defecto
                    if (!foundOldColegio && originalSelectedColegioId !== "") {
                         colegioSelect.value = "";
                    }
                } else {
                    colegioSelect.value = ""; // Si no hay municipio seleccionado, el colegio también debe resetearse
                }
            }

            // Ejecutar al cargar la página para aplicar el filtro inicial si old() tiene valores
            filterColegios();

            // Ejecutar cada vez que cambia el municipio seleccionado
            municipioSelect.addEventListener('change', filterColegios);
        });


        // Mostrar/ocultar campo "Otro" para redes sociales
        const socialMediaCheckboxes = document.querySelectorAll('input[name="red_social[]"]');
        const otroRedSocialDiv = document.getElementById('otro_red_social_div');
        const otroRedSocialInput = otroRedSocialDiv.querySelector('input[name="otro_red_social"]');
        const otroSocialMediaId = {{ $socialMediaOptions->firstWhere('name', 'Otro')->id ?? 'null' }};

        socialMediaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (otroSocialMediaId && this.value == otroSocialMediaId) {
                    if (this.checked) {
                        otroRedSocialDiv.style.display = 'block';
                    } else {
                        otroRedSocialDiv.style.display = 'none';
                        otroRedSocialInput.value = ''; // Limpiar el input si se desmarca
                    }
                }
            });
        });

        window.addEventListener('load', function() {
            const oldRedSocials = @json(old('red_social', []));
            if (otroSocialMediaId && oldRedSocials.includes(otroSocialMediaId.toString())) {
                otroRedSocialDiv.style.display = 'block';
            } else {
                otroRedSocialDiv.style.display = 'none';
            }
        });


        // Mostrar/ocultar campo "Otra cosa" para tipos de acoso
        const bullyingTypeCheckboxes = document.querySelectorAll('input[name="que_esta_pasando[]"]');
        const otroQueEstaPasandoDiv = document.getElementById('otro_que_esta_pasando_div');
        const otroQueEstaPasandoTextarea = otroQueEstaPasandoDiv.querySelector('textarea[name="otro_que_esta_pasando"]');
        const otraCosaBullyingTypeId = {{ $bullyingTypeOptions->firstWhere('description', 'Otra cosa (escríbela)')->id ?? 'null' }};

        bullyingTypeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (otraCosaBullyingTypeId && this.value == otraCosaBullyingTypeId) {
                    if (this.checked) {
                        otroQueEstaPasandoDiv.style.display = 'block';
                    } else {
                        otroQueEstaPasandoDiv.style.display = 'none';
                        otroQueEstaPasandoTextarea.value = ''; // Limpiar el campo si se desmarca
                    }
                }
            });
        });

        window.addEventListener('load', function() {
            const oldQueEstaPasando = @json(old('que_esta_pasando', []));
            if (otraCosaBullyingTypeId && oldQueEstaPasando.includes(otraCosaBullyingTypeId.toString())) {
                otroQueEstaPasandoDiv.style.display = 'block';
            } else {
                otroQueEstaPasandoDiv.style.display = 'none';
            }
        });


        // Mostrar/ocultar campo de nombre del agresor
        document.querySelectorAll('input[name="agresor_conocido"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const nombreAgresorDiv = document.getElementById('agresor_nombre_div');
                const nombreAgresorInput = nombreAgresorDiv.querySelector('input[name="agresor_nombre"]');
                if (this.value === 'si' || this.value === 'sospecho quien es') {
                    nombreAgresorDiv.style.display = 'block';
                } else {
                    nombreAgresorDiv.style.display = 'none';
                    nombreAgresorInput.value = '';
                }
            });
        });

        window.addEventListener('load', function() {
            const selectedAgresorConocido = document.querySelector('input[name="agresor_conocido"]:checked');
            if (selectedAgresorConocido && (selectedAgresorConocido.value === 'si' || selectedAgresorConocido.value === 'sospecho quien es')) {
                document.getElementById('agresor_nombre_div').style.display = 'block';
            } else {
                document.getElementById('agresor_nombre_div').style.display = 'none';
            }
        });


        // Mostrar/ocultar campo de línea telefónica
        document.querySelectorAll('input[name="reportado_otro_medio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const cualLineaDiv = document.getElementById('reportado_cual_linea_div');
                const cualLineaInput = cualLineaDiv.querySelector('input[name="reportado_cual_linea"]');
                if (this.value === 'Sí, llamé a la línea telefónica') {
                    cualLineaDiv.style.display = 'block';
                } else {
                    cualLineaDiv.style.display = 'none';
                    cualLineaInput.value = '';
                }
            });
        });

        window.addEventListener('load', function() {
            const selectedReportadoOtroMedio = document.querySelector('input[name="reportado_otro_medio"]:checked');
            if (selectedReportadoOtroMedio && selectedReportadoOtroMedio.value === 'Sí, llamé a la línea telefónica') {
                document.getElementById('reportado_cual_linea_div').style.display = 'block';
            } else {
                document.getElementById('reportado_cual_linea_div').style.display = 'none';
            }
        });


        // Validación adicional del lado del cliente (mejorada)
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessage = [];

            // Validar que al menos una red social esté seleccionada
            const redesSociales = document.querySelectorAll('input[name="red_social[]"]:checked');
            if (redesSociales.length === 0) {
                isValid = false;
                errorMessage.push('• Por favor selecciona al menos una red social donde ocurrieron los hechos.');
            }

            // Validar que al menos una opción de "qué está pasando" esté seleccionada
            const queEstaPasando = document.querySelectorAll('input[name="que_esta_pasando[]"]:checked');
            if (queEstaPasando.length === 0) {
                isValid = false;
                errorMessage.push('• Por favor selecciona al menos una opción de lo que está pasando.');
            }

            // Validar que al menos una opción de "cómo se siente" esté seleccionada
            const comoSeSiente = document.querySelectorAll('input[name="como_te_sientes[]"]:checked');
            if (comoSeSiente.length === 0) {
                isValid = false;
                errorMessage.push('• Por favor selecciona al menos una opción de cómo te sientes.');
            }

            // Validar edad (calculada)
            const edad = parseInt(document.getElementById('edad').value);
            if (isNaN(edad) || edad < 0 || edad > 120) {
                isValid = false;
                errorMessage.push('• Por favor verifica que la fecha de nacimiento sea correcta.');
            }

            // Validar campos de tiempo (al menos uno > 0)
            const tiempoDias = parseInt(document.getElementById('tiempo_dias').value) || 0;
            const tiempoMeses = parseInt(document.getElementById('tiempo_meses').value) || 0;
            const tiempoAnios = parseInt(document.getElementById('tiempo_anios').value) || 0;

            if (tiempoDias === 0 && tiempoMeses === 0 && tiempoAnios === 0) {
                isValid = false;
                errorMessage.push('• Debes especificar un tiempo transcurrido (días, meses o años) que sea mayor a cero.');
            }


            // Si hay errores, prevenir el envío y mostrar un resumen
            if (!isValid) {
                e.preventDefault();
                alert('¡Ups! Parece que faltan algunos datos o hay errores:\n' + errorMessage.join('\n'));
            } else {
                // Confirmar envío si todas las validaciones pasan
                if (!confirm('¿Estás seguro de que quieres enviar esta denuncia? Una vez enviada, será procesada de manera confidencial.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>