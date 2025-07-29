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
        try {
            // Obtener municipios activos
            $municipios = Municipio::where('activo', true)->orderBy('nombre')->get();
            
            // Obtener colegios activos
            $colegios = Colegio::where('activo', true)->orderBy('nombre')->get();
            
            // Obtener opciones del formulario
            $socialMediaOptions = SocialMedia::all();
            $bullyingTypeOptions = BullyingType::all();
            $feelingOptions = Feeling::all();
            $reportChannelOptions = ReportChannel::all();

            return view('reports.create', compact(
                'municipios',
                'colegios',
                'socialMediaOptions',
                'bullyingTypeOptions',
                'feelingOptions',
                'reportChannelOptions'
            ));
        } catch (\Exception $e) {
            // Si algún modelo no existe, crear datos básicos para prueba
            $municipios = collect([]);
            $colegios = collect([]);
            $socialMediaOptions = collect([]);
            $bullyingTypeOptions = collect([]);
            $feelingOptions = collect([]);
            $reportChannelOptions = collect([]);

            // Intentar obtener al menos los municipios que sabemos que existen
            try {
                $municipios = Municipio::where('activo', true)->orderBy('nombre')->get();
            } catch (\Exception $e2) {
                // Si no existe ni siquiera la tabla municipios, mostrar error
                return redirect()->back()->with('error', 'Error al cargar el formulario. Faltan configuraciones en la base de datos.');
            }

            return view('reports.create', compact(
                'municipios',
                'colegios',
                'socialMediaOptions',
                'bullyingTypeOptions',
                'feelingOptions',
                'reportChannelOptions'
            ));
        }
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

            'red_social' => 'nullable|array',
            'red_social.*' => 'exists:social_media_options,id',
            'otro_red_social' => 'nullable|string|max:255',

            'que_esta_pasando' => 'nullable|array',
            'que_esta_pasando.*' => 'exists:bullying_type_options,id',
            'otro_que_esta_pasando' => 'nullable|string|max:255',

            'afectado_quien' => ['required', Rule::in(['Soy yo', 'Es otra persona', 'Prefiero no decir', 'Otra persona y yo'])],
            'agresor_conocido' => ['required', Rule::in(['si', 'no', 'sospecho quien es'])],
            'agresor_nombre' => 'nullable|string|max:255',

            'tiempo_dias' => 'nullable|integer|min:0',
            'tiempo_meses' => 'nullable|integer|min:0',
            'tiempo_anios' => 'nullable|integer|min:0',

            'como_te_sientes' => 'nullable|array',
            'como_te_sientes.*' => 'exists:feeling_options,id',

            'reportado_otro_medio' => 'required|string',
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
            'in' => 'La opción seleccionada para ":attribute" no es válida.',
            'integer' => 'El campo ":attribute" debe ser un número entero.',
            'file' => 'El archivo en ":attribute" no tiene un formato válido.',
            'mimes' => 'El tipo de archivo de evidencia no es permitido.',
            'max.file' => 'El archivo de evidencia no debe superar los :max KB.',
        ];

        $request->validate($rules, $messages);

        $dias = (int) ($request->tiempo_dias ?? 0);
        $meses = (int) ($request->tiempo_meses ?? 0);
        $anios = (int) ($request->tiempo_anios ?? 0);

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
        
        // Crear un estado por defecto si no existe
        $estadoAbierta = DenunciaEstado::firstOrCreate(
            ['nombre' => 'Abierta'],
            ['descripcion' => 'Denuncia recién recibida']
        );

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
            'denuncia_estado_id' => $estadoAbierta->id,
        ]);

        // Sincronizar relaciones solo si existen los datos
        if ($request->has('red_social') && is_array($request->red_social)) {
            $report->socialMedia()->sync($request->input('red_social', []));
        }
        if ($request->has('que_esta_pasando') && is_array($request->que_esta_pasando)) {
            $report->bullyingTypes()->sync($request->input('que_esta_pasando', []));
        }
        if ($request->has('como_te_sientes') && is_array($request->como_te_sientes)) {
            $report->feelings()->sync($request->input('como_te_sientes', []));
        }

        return redirect()->route('report.success')->with('success', '¡Tu denuncia ha sido enviada con éxito! Gracias por tu valentía.');
    }

    /**
     * Muestra una lista de todas las denuncias para el panel administrativo.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Report::query();

        // Intentar cargar la relación estado si existe
        try {
            $query->with(['estado']);
        } catch (\Exception $e) {
            // Si no existe la relación, continuar sin ella
        }

        // Lógica de filtrado basada en el rol del usuario
        if ($user->role === 'admin') {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role === 'supervisor') {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $reports = $query->latest()->paginate(10);

        // Cálculo básico de estadísticas
        $stats = [];
        $baseStatsQuery = Report::query();

        if ($user->role === 'admin') {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0');
            }
        } elseif ($user->role === 'supervisor') {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0');
            }
        }

        $stats['total_denuncias'] = $baseStatsQuery->count();
        $stats['denuncias_abiertas'] = 0;
        $stats['denuncias_en_tramite'] = 0;
        $stats['denuncias_pendiente_cierre'] = 0;
        $stats['denuncias_cerradas'] = 0;

        // Intentar calcular estadísticas por estado si la relación existe
        try {
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
        } catch (\Exception $e) {
            // Si no existe la relación, usar valores por defecto
        }

        return view('reports.index', compact('reports', 'stats'));
    }

    /**
     * Muestra los detalles de una denuncia específica.
     */
    public function show(Report $report)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
        } elseif ($user->role === 'supervisor') {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
        }

        // Intentar cargar relaciones si existen
        try {
            $report->load(['socialMedia', 'bullyingTypes', 'feelings', 'estado', 'seguimientos.user', 'seguimientos.estadoAnterior', 'seguimientos.estadoNuevo']);
        } catch (\Exception $e) {
            // Si no existen todas las relaciones, continuar
        }

        $denunciaEstados = collect([]);
        try {
            $denunciaEstados = DenunciaEstado::all();
        } catch (\Exception $e) {
            // Si no existe la tabla, usar colección vacía
        }

        return view('reports.show', compact('report', 'denunciaEstados'));
    }

    /**
     * Actualiza el estado de una denuncia y añade un seguimiento.
     * NUEVA LÓGICA DE PERMISOS:
     * - Super Admin: Puede hacer todo
     * - Admin: Puede hacer todo EXCEPTO devolver de "En Trámite" a "Abierta"
     * - Supervisor: Solo puede avanzar estados (Abierta -> En Trámite -> Pendiente de Cierre)
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

        // Crear estados si no existen
        $estadoAbierta = DenunciaEstado::firstOrCreate(['nombre' => 'Abierta'], ['descripcion' => 'Denuncia recién recibida']);
        $estadoEnTramite = DenunciaEstado::firstOrCreate(['nombre' => 'En Trámite'], ['descripcion' => 'Denuncia en proceso']);
        $estadoPendienteCierre = DenunciaEstado::firstOrCreate(['nombre' => 'Pendiente de Cierre'], ['descripcion' => 'Denuncia lista para cerrar']);
        $estadoCerrada = DenunciaEstado::firstOrCreate(['nombre' => 'Cerrada'], ['descripcion' => 'Denuncia finalizada']);

        // Verificar permisos de acceso a la denuncia
        if ($currentUser->role === 'admin' && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }
        if ($currentUser->role === 'supervisor' && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }

        // LÓGICA DE PERMISOS POR ROL
        if ($currentUser->role === 'supervisor') {
            // SUPERVISOR: Solo puede avanzar estados, no retroceder ni cerrar
            
            // Puede pasar de Abierta a En Trámite
            if ($oldStatus->id === $estadoAbierta->id && $newStatus->id === $estadoEnTramite->id) {
                // Permitido
            }
            // Puede pasar de En Trámite a Pendiente de Cierre
            elseif ($oldStatus->id === $estadoEnTramite->id && $newStatus->id === $estadoPendienteCierre->id) {
                if ($report->seguimientos->count() < 1) {
                    return back()->with('error', 'Debe haber al menos una anotación de seguimiento antes de marcar como "Pendiente de Cierre".');
                }
            }
            // No puede hacer otras transiciones
            else {
                return back()->with('error', 'Como Supervisor, solo puedes avanzar el estado de las denuncias (Abierta → En Trámite → Pendiente de Cierre).');
            }
        }
        
        elseif ($currentUser->role === 'admin') {
            // ADMIN: Puede hacer todo EXCEPTO devolver de "En Trámite" a "Abierta"
            
            // No puede devolver de En Trámite a Abierta
            if ($oldStatus->id === $estadoEnTramite->id && $newStatus->id === $estadoAbierta->id) {
                return back()->with('error', 'No puedes devolver una denuncia de "En Trámite" a "Abierta". Solo el Super Administrador puede hacerlo.');
            }
            
            // Validaciones específicas para Admin
            if ($newStatus->id === $estadoPendienteCierre->id) {
                if ($oldStatus->id !== $estadoEnTramite->id) {
                    return back()->with('error', 'Solo se puede pasar a "Pendiente de Cierre" desde "En Trámite".');
                }
            }
            
            if ($newStatus->id === $estadoCerrada->id) {
                if ($oldStatus->id !== $estadoPendienteCierre->id) {
                    return back()->with('error', 'Solo se puede cerrar una denuncia si está en estado "Pendiente de Cierre".');
                }
            }
        }
        
        // SUPER ADMIN: Sin restricciones (no necesita validaciones especiales)
        
        // Actualizar el estado
        $report->denuncia_estado_id = $newStatus->id;
        $report->save();

        // Crear el seguimiento
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

        if ($currentUser->role === 'admin' && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }
        if ($currentUser->role === 'supervisor' && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }

        $estadoAbierta = DenunciaEstado::firstOrCreate(['nombre' => 'Abierta'], ['descripcion' => 'Denuncia recién recibida']);
        $estadoEnTramite = DenunciaEstado::firstOrCreate(['nombre' => 'En Trámite'], ['descripcion' => 'Denuncia en proceso']);

        $oldStatusId = $report->denuncia_estado_id;
        $newStatusId = $report->denuncia_estado_id;

        // Si la denuncia está "Abierta", cambiarla automáticamente a "En Trámite" al agregar seguimiento
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

    /**
     * Métodos específicos para diferentes roles (si los necesitas mantener)
     */
    public function indexByAdmin()
    {
        return $this->index(); // Redirigir al método principal
    }

    public function showByAdmin(Report $report)
    {
        return $this->show($report); // Redirigir al método principal
    }

    public function indexBySupervisor()
    {
        return $this->index(); // Redirigir al método principal
    }

    public function showBySupervisor(Report $report)
    {
        return $this->show($report); // Redirigir al método principal
    }
}