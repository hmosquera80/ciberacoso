<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialMedia;
use App\Models\BullyingType;
use App\Models\Feeling;
use App\Models\ReportChannel;
use App\Models\Report;
use App\Models\Municipio;
use App\Models\Colegio;
use App\Models\DenunciaEstado;
use App\Models\DenunciaSeguimiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Muestra el formulario de creación de una nueva denuncia.
     */
    public function create()
    {
        $socialMediaOptions = SocialMedia::all();
        $bullyingTypeOptions = BullyingType::all();
        $feelingOptions = Feeling::all();
        $reportChannelOptions = ReportChannel::all();
        $municipios = Municipio::where('activo', true)->get();
        $colegios = Colegio::where('activo', true)->get();

        return view('reports.create', compact(
            'socialMediaOptions',
            'bullyingTypeOptions',
            'feelingOptions',
            'reportChannelOptions',
            'municipios',
            'colegios'
        ));
    }

    /**
     * Almacena una nueva denuncia en la base de datos.
     */
    public function store(Request $request)
    {
        $rules = [
            'denunciante_nombre_completo' => 'required|string|max:255',
            'denunciante_fecha_nacimiento' => 'required|date|before_or_equal:today',
            'denunciante_municipio_id' => 'required|exists:municipios,id',
            'denunciante_colegio_id' => 'required|exists:colegios,id',
            'denunciante_curso_grado' => 'required|string|max:255',
            'denunciante_identificacion' => 'required|string|max:255',

            'red_social' => 'required|array|min:1',
            'red_social.*' => 'exists:social_media,id',
            'otro_red_social' => 'nullable|string|max:255',

            'que_esta_pasando' => 'required|array|min:1',
            'que_esta_pasando.*' => 'exists:bullying_types,id',
            'otro_que_esta_pasando' => 'nullable|string|max:255',

            'afectado_quien' => ['required', Rule::in(['Soy yo', 'Es otra persona', 'Prefiero no decir', 'Otra persona y yo'])],
            'agresor_conocido' => ['required', Rule::in(['si', 'no', 'sospecho quien es'])],
            'agresor_nombre' => 'nullable|string|max:255',

            'tiempo_dias' => 'nullable|integer|min:0',
            'tiempo_meses' => 'nullable|integer|min:0',
            'tiempo_anios' => 'nullable|integer|min:0',

            'como_te_sientes' => 'required|array|min:1',
            'como_te_sientes.*' => 'exists:feelings,id',

            'reportado_otro_medio' => ['required', Rule::in(ReportChannel::pluck('name')->toArray())],
            'reportado_cual_linea' => 'nullable|string|max:255',

            'resumen_hechos' => 'required|string|min:10|max:1000',
            'contacto_deseado' => ['required', Rule::in(['Sí, quiero que me llamen o escriban', 'No por ahora, solo quería contar lo que me pasa', 'Me gustaría recibir ayuda después'])],

            'evidencia_file' => 'nullable|file|mimes:jpeg,png,pdf,doc,docx,mp4,mov|max:20480',
        ];

        $messages = [
            'required' => 'El campo ":attribute" es obligatorio.',
            'string' => 'El formato del campo ":attribute" es incorrecto.',
            'max' => 'El campo ":attribute" no debe exceder los :max caracteres.',
            'min.string' => 'El campo ":attribute" debe tener al menos :min caracteres.',
            'min.array' => 'Debe seleccionar al menos :min opción en ":attribute".',
            'date' => 'La fecha no tiene un formato válido.',
            'before_or_equal' => 'La fecha de nacimiento no puede ser en el futuro.',
            'array' => 'Debe seleccionar al menos una opción en ":attribute".',
            'exists' => 'Una de las opciones seleccionadas en ":attribute" no es válida.',
            'in' => 'La opción seleccionada para ":attribute" no es válida. Por favor, elige una de las opciones disponibles.',
            'integer' => 'El campo ":attribute" debe ser un número entero.',
            'file' => 'El archivo en ":attribute" no tiene un formato válido.',
            'mimes' => 'El tipo de archivo de evidencia no es permitido. Formatos aceptados: :values.',
            'max.file' => 'El archivo de evidencia no debe superar los :max KB.',
            'resumen_hechos.min' => 'El "Resumen de los hechos" debe tener al menos :min caracteres.',
        ];

        $attributes = [
            'denunciante_nombre_completo' => 'Nombre completo del denunciante',
            'denunciante_fecha_nacimiento' => 'Fecha de nacimiento',
            'denunciante_municipio_id' => 'Municipio',
            'denunciante_colegio_id' => 'Nombre del colegio',
            'denunciante_curso_grado' => 'Curso / grado',
            'denunciante_identificacion' => '# de Identificación',
            'red_social' => 'Redes sociales',
            'otro_red_social' => 'Otra red social',
            'que_esta_pasando' => '¿Qué está pasando?',
            'otro_que_esta_pasando' => 'Otra cosa que está pasando',
            'afectado_quien' => 'Persona afectada',
            'agresor_conocido' => 'Conocimiento del agresor',
            'agresor_nombre' => 'Nombre del agresor',
            'tiempo_dias' => 'Días de ocurrencia',
            'tiempo_meses' => 'Meses de ocurrencia',
            'tiempo_anios' => 'Años de ocurrencia',
            'como_te_sientes' => 'Cómo te sientes',
            'reportado_otro_medio' => 'Reporte previo',
            'reportado_cual_linea' => 'Línea telefónica de reporte',
            'resumen_hechos' => 'Resumen de los hechos',
            'contacto_deseado' => 'Deseo de contacto',
            'evidencia_file' => 'Archivo de evidencia',
        ];

        $request->validate($rules, $messages, $attributes);

        $dias = (int) $request->tiempo_dias;
        $meses = (int) $request->tiempo_meses;
        $anios = (int) $request->tiempo_anios;

        if ($dias === 0 && $meses === 0 && $anios === 0) {
            return back()->withErrors(['tiempo_transcurrido' => 'Debes especificar un tiempo transcurrido (días, meses o años) que sea mayor a cero.'])
                        ->withInput();
        }

        $fechaNacimiento = Carbon::parse($request->denunciante_fecha_nacimiento);
        $edad = $fechaNacimiento->age;

        $evidenciaPath = null;
        $tienePruebas = false;

        if ($request->hasFile('evidencia_file')) {
            $evidenciaPath = $request->file('evidencia_file')->store('evidencias', 'public');
            $tienePruebas = true;
        }

        $municipioNombre = Municipio::find($request->denunciante_municipio_id)->nombre;
        $colegioNombre = Colegio::find($request->denunciante_colegio_id)->nombre;

        $estadoAbiertaId = DenunciaEstado::where('nombre', 'Abierta')->first()->id;

        $report = Report::create([
            'denunciante_nombre_completo' => $request->denunciante_nombre_completo,
            'denunciante_fecha_nacimiento' => $request->denunciante_fecha_nacimiento,
            'denunciante_edad' => $edad,
            'denunciante_municipio' => $municipioNombre,
            'denunciante_colegio' => $colegioNombre,
            'denunciante_curso_grado' => $request->denunciante_curso_grado,
            'denunciante_identificacion' => $request->denunciante_identificacion,
            'afectado_quien' => $request->afectado_quien,
            'agresor_conocido' => $request->agresor_conocido,
            'agresor_nombre' => $request->agresor_nombre,
            'tiempo_dias' => $request->tiempo_dias ?? 0,
            'tiempo_meses' => $request->tiempo_meses ?? 0,
            'tiempo_anios' => $request->tiempo_anios ?? 0,
            'reportado_otro_medio' => $request->reportado_otro_medio,
            'reportado_cual_linea' => $request->reportado_cual_linea,
            'resumen_hechos' => $request->resumen_hechos,
            'contacto_deseado' => $request->contacto_deseado,
            'tiene_pruebas' => $tienePruebas,
            'evidencia_path' => $evidenciaPath,
            'denuncia_estado_id' => $estadoAbiertaId,
        ]);

        $report->socialMedia()->sync($request->input('red_social', []));
        $report->bullyingTypes()->sync($request->input('que_esta_pasando', []));
        $report->feelings()->sync($request->input('como_te_sientes', []));

        return redirect()->route('report.success')->with('success', '¡Tu denuncia ha sido enviada con éxito! Gracias por tu valentía.');
    }

    /**
     * Muestra una lista de todas las denuncias para el panel administrativo.
     * La lógica de filtrado por rol se implementa aquí.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Report::with(['estado']);

        if ($user->isAdmin()) {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $reports = $query->latest()->paginate(10);

        $stats = [];
        $baseStatsQuery = Report::query();

        if ($user->isAdmin()) {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0');
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0');
            }
        }

        $stats['total_denuncias'] = $baseStatsQuery->count();
        $stats['denuncias_abiertas'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'Abierta');
        })->count();
        $stats['denuncias_en_tramite'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'En Trámite');
        })->count();
        $stats['denuncias_pendiente_cierre'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'Pendiente de Cierre');
        })->count();
        $stats['denuncias_cerradas'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'Cerrada');
        })->count();

        if ($user->colegio) {
            $stats['mi_colegio'] = $user->colegio->nombre;
            if ($user->colegio->municipio) {
                $stats['mi_municipio'] = $user->colegio->municipio->nombre;
            }
        } else {
            $stats['mi_colegio'] = 'Sistema Global';
        }

        return view('reports.index', compact('reports', 'stats'));
    }

    /**
     * Muestra los detalles de una denuncia específica.
     * La lógica de permisos para ver el detalle se implementa aquí.
     */
    public function show(Report $report)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
        }

        $report->load(['socialMedia', 'bullyingTypes', 'feelings', 'estado', 'seguimientos.user', 'seguimientos.estadoAnterior', 'seguimientos.estadoNuevo']);
        $denunciaEstados = DenunciaEstado::all();

        return view('reports.show', compact('report', 'denunciaEstados'));
    }

    /**
     * Actualiza el estado de una denuncia y añade un seguimiento.
     * Accesible por Super Admin, Admin, Supervisor.
     */
    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'new_status_id' => 'required|exists:denuncia_estados,id',
            'anotacion_estado' => 'required|string|min:10',
        ], [
            'anotacion_estado.required' => 'La anotación es obligatoria al cambiar el estado.',
            'anotacion_estado.min' => 'La anotación debe tener al menos :min caracteres.',
        ]);

        $currentUser = Auth::user();
        $oldStatus = $report->estado;
        $newStatus = DenunciaEstado::find($request->new_status_id);

        $estadoAbierta = DenunciaEstado::where('nombre', 'Abierta')->first();
        $estadoEnTramite = DenunciaEstado::where('nombre', 'En Trámite')->first();
        $estadoPendienteCierre = DenunciaEstado::where('nombre', 'Pendiente de Cierre')->first();
        $estadoCerrada = DenunciaEstado::where('nombre', 'Cerrada')->first();

        if ($currentUser->isAdmin() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }
        if ($currentUser->isSupervisor() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }

        if (!$currentUser->isSuperAdmin()) {
            if ($newStatus->id === $estadoEnTramite->id) {
                if ($oldStatus->id !== $estadoAbierta->id || $report->denuncia_estado_id !== $estadoAbierta->id) {
                    return back()->with('error', 'Solo se puede pasar a "En Trámite" si la denuncia está "Abierta".');
                }
                if ($report->seguimientos->isNotEmpty() && $oldStatus->id === $estadoEnTramite->id) {
                    return back()->with('error', 'La denuncia ya está en trámite, no se puede cambiar a este estado de nuevo.');
                }
            }

            if ($newStatus->id === $estadoPendienteCierre->id) {
                if ($oldStatus->id !== $estadoEnTramite->id) {
                    return back()->with('error', 'Solo se puede pasar a "Pendiente de Cierre" desde "En Trámite".');
                }
                if ($report->seguimientos->count() < 1) {
                    return back()->with('error', 'Debe haber al menos una anotación de seguimiento antes de marcar como "Pendiente de Cierre".');
                }
                if (!$currentUser->isSupervisor()) {
                    return back()->with('error', 'Solo un Supervisor puede marcar una denuncia como "Pendiente de Cierre".');
                }
            }

            if ($newStatus->id === $estadoCerrada->id) {
                if ($oldStatus->id !== $estadoPendienteCierre->id) {
                    return back()->with('error', 'Solo se puede cerrar una denuncia si está en estado "Pendiente de Cierre".');
                }
                if (!$currentUser->isSupervisor()) {
                    return back()->with('error', 'Solo un Supervisor puede cerrar definitivamente una denuncia.');
                }
            }

            if ($oldStatus->id > $newStatus->id && $oldStatus->id !== $newStatus->id) {
                if ($oldStatus->id == $estadoPendienteCierre->id && $newStatus->id == $estadoCerrada->id) {
                    // Esta transición está bien
                } else {
                    return back()->with('error', 'No se permite retroceder el estado de una denuncia.');
                }
            }
        }

        $report->denuncia_estado_id = $newStatus->id;
        $report->save();

        DenunciaSeguimiento::create([
            'report_id' => $report->id,
            'user_id' => $currentUser->id,
            'anotacion' => $request->anotacion_estado,
            'denuncia_estado_anterior_id' => $oldStatus->id,
            'denuncia_estado_nuevo_id' => $newStatus->id,
        ]);

        return back()->with('success', 'Estado de la denuncia actualizado y seguimiento registrado.');
    }

    /**
     * Añade un seguimiento/anotación a una denuncia sin cambiar su estado.
     * Accesible por Super Admin, Admin, Supervisor.
     */
    public function addSeguimiento(Request $request, Report $report)
    {
        $request->validate([
            'anotacion' => 'required|string|min:10',
        ], [
            'anotacion.required' => 'La anotación es obligatoria.',
            'anotacion.min' => 'La anotacion debe tener al menos :min caracteres.',
        ]);

        $currentUser = Auth::user();

        if ($currentUser->isAdmin() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }
        if ($currentUser->isSupervisor() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }

        $estadoAbierta = DenunciaEstado::where('nombre', 'Abierta')->first();
        $estadoEnTramite = DenunciaEstado::where('nombre', 'En Trámite')->first();

        $oldStatusId = $report->denuncia_estado_id;
        $newStatusId = $report->denuncia_estado_id;

        if ($oldStatusId === $estadoAbierta->id) {
            $newStatusId = $estadoEnTramite->id;
            $report->denuncia_estado_id = $newStatusId;
            $report->save();
        }

        DenunciaSeguimiento::create([
            'report_id' => $report->id,
            'user_id' => $currentUser->id,
            'anotacion' => $request->anotacion,
            'denuncia_estado_anterior_id' => $oldStatusId,
            'denuncia_estado_nuevo_id' => $newStatusId,
        ]);

        return back()->with('success', 'Anotación agregada exitosamente.');
    }
}