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
use App\Models\DenunciaSeguimiento; // Asegúrate de importar DenunciaSeguimiento
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Necesario para Auth::user()

class ReportController extends Controller
{
    // ... (métodos create y store sin cambios)

    /**
     * Muestra una lista de todas las denuncias para el panel administrativo.
     * La lógica de filtrado por rol se implementa aquí.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Report::with(['estado']); // Siempre cargar el estado

        // Lógica de filtrado basada en el rol del usuario
        if ($user->isAdmin()) {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0'); // No mostrar resultados si no tiene colegio asignado
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio) {
                $colegioNombre = $user->colegio->nombre;
                $query->where('denunciante_colegio', $colegioNombre);
            } else {
                $query->whereRaw('1 = 0'); // No mostrar resultados si no tiene colegio asignado
            }
            // Para supervisores, también podríamos filtrar por las denuncias que tiene asignadas
            // $query->whereHas('seguimientos', function ($q) use ($user) {
            //     $q->where('user_id', $user->id);
            // });
        }
        // Super Admin ve todas las denuncias (no necesita filtro adicional)

        $reports = $query->latest()->paginate(10);

        // --- CÁLCULO DE ESTADÍSTICAS PARA EL DASHBOARD ---
        $stats = [];
        $baseStatsQuery = Report::query(); // Query base para estadísticas

        if ($user->isAdmin()) {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0'); // No contar nada si el admin no tiene colegio
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio) {
                $baseStatsQuery->where('denunciante_colegio', $user->colegio->nombre);
            } else {
                $baseStatsQuery->whereRaw('1 = 0'); // No contar nada si el supervisor no tiene colegio
            }
            // Si quieres que el supervisor solo vea stats de sus asignadas, la query cambia aquí
        }

        $stats['total_denuncias'] = $baseStatsQuery->count();
        $stats['denuncias_abiertas'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'Abierta');
        })->count();
        $stats['denuncias_en_tramite'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'En Trámite');
        })->count();
        $stats['denuncias_cerradas'] = (clone $baseStatsQuery)->whereHas('estado', function($q) {
            $q->where('nombre', 'Cerrada');
        })->count();
        // Nota: 'Pendiente de Cierre' no tiene una tarjeta propia en el diseño, se sumaría en 'En Trámite' o se crea una nueva.
        // Si quieres una tarjeta 'Pendiente de Cierre', añade una aquí.

        // Añadir información de la entidad si aplica
        if ($user->colegio) {
            $stats['mi_colegio'] = $user->colegio->nombre;
            if ($user->colegio->municipio) {
                $stats['mi_municipio'] = $user->colegio->municipio->nombre;
            }
        } else {
            $stats['mi_colegio'] = 'Sistema Global'; // Para Super Admin
        }


        return view('reports.index', compact('reports', 'stats')); // Pasar $stats a la vista
    }


    /**
     * Muestra los detalles de una denuncia específica.
     * La lógica de permisos para ver el detalle se implementa aquí.
     */
    public function show(Report $report)
    {
        $user = Auth::user();

        // Verificar permisos para ver la denuncia (duplicado de lógica de index, pero más seguro aquí)
        if ($user->isAdmin()) {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
        } elseif ($user->isSupervisor()) {
            if ($user->colegio && $report->denunciante_colegio !== $user->colegio->nombre) {
                return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            }
            // Si el supervisor solo puede ver asignadas, añadir aquí:
            // if ($report->seguimientos->where('user_id', $user->id)->isEmpty()) {
            //     return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta denuncia.');
            // }
        }
        // Super Admin puede ver todas las denuncias

        $report->load(['socialMedia', 'bullyingTypes', 'feelings', 'estado', 'seguimientos.user', 'seguimientos.estadoAnterior', 'seguimientos.estadoNuevo']);
        $denunciaEstados = DenunciaEstado::all(); // Se usa en la vista 'show' para el cambio de estado

        return view('reports.show', compact('report', 'denunciaEstados'));
    }

    /**
     * Actualiza el estado de una denuncia y añade un seguimiento.
     * Accesible por Super Admin, Admin, Supervisor.
     * Define la lógica de transición de estados y permisos por rol.
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

        // 1. Permiso de acceso a la denuncia para gestionar (similar a show)
        if ($currentUser->isAdmin() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }
        if ($currentUser->isSupervisor() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para gestionar esta denuncia.');
        }
        // Super Admin tiene control total, no necesita filtros de acceso aquí.


        // 2. Lógica de Transiciones de Estado y Permisos por Rol
        if (!$currentUser->isSuperAdmin()) { // Super Admin no tiene restricciones de transición
            // Transición a 'En Trámite'
            if ($newStatus->id === $estadoEnTramite->id) {
                if ($oldStatus->id !== $estadoAbierta->id || $report->denuncia_estado_id !== $estadoAbierta->id) { // Solo si estaba Abierta
                    return back()->with('error', 'Solo se puede pasar a "En Trámite" si la denuncia está "Abierta".');
                }
                // Si la denuncia no tiene seguimientos, esta es la primera anotación, se permite a Admin/Supervisor
                // Si ya tiene seguimientos, significa que ya está en trámite, no se puede volver a pasar a 'En Trámite'
                if ($report->seguimientos->isNotEmpty() && $oldStatus->id === $estadoEnTramite->id) {
                    return back()->with('error', 'La denuncia ya está en trámite, no se puede cambiar a este estado de nuevo.');
                }
            }

            // Transición a 'Pendiente de Cierre'
            if ($newStatus->id === $estadoPendienteCierre->id) {
                if ($oldStatus->id !== $estadoEnTramite->id) { // Solo desde En Trámite
                    return back()->with('error', 'Solo se puede pasar a "Pendiente de Cierre" desde "En Trámite".');
                }
                if ($report->seguimientos->count() < 1) { // Debe haber al menos una anotación previa (no la inicial)
                    return back()->with('error', 'Debe haber al menos una anotación de seguimiento antes de marcar como "Pendiente de Cierre".');
                }
                if (!$currentUser->isSupervisor()) { // Solo Supervisor
                    return back()->with('error', 'Solo un Supervisor puede marcar una denuncia como "Pendiente de Cierre".');
                }
            }

            // Transición a 'Cerrada'
            if ($newStatus->id === $estadoCerrada->id) {
                if ($oldStatus->id !== $estadoPendienteCierre->id) { // Solo desde Pendiente de Cierre
                    return back()->with('error', 'Solo se puede cerrar una denuncia si está en estado "Pendiente de Cierre".');
                }
                if (!$currentUser->isSupervisor()) { // Solo Supervisor
                    return back()->with('error', 'Solo un Supervisor puede cerrar definitivamente una denuncia.');
                }
            }

            // Evitar retroceso de estados (excepto si Super Admin)
            // Asumiendo que los IDs de estados son progresivos
            if ($oldStatus->id > $newStatus->id && $oldStatus->id !== $newStatus->id) {
                return back()->with('error', 'No se permite retroceder el estado de una denuncia.');
            }
        }
        // Fin de la lógica de validación de roles y transiciones para Admin/Supervisor

        // Actualizar el estado del reporte
        $report->denuncia_estado_id = $newStatus->id;
        $report->save();

        // Registrar el seguimiento
        DenunciaSeguimiento::create([
            'report_id' => $report->id,
            'user_id' => $currentUser->id,
            'anotacion' => $request->anotacion_estado, // Usamos 'anotacion_estado' del formulario
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
            'anotacion.min' => 'La anotación debe tener al menos :min caracteres.',
        ]);

        $currentUser = Auth::user();

        // Verificar permisos para añadir seguimiento (misma lógica que para show/updateStatus)
        if ($currentUser->isAdmin() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }
        if ($currentUser->isSupervisor() && ($currentUser->colegio && $report->denunciante_colegio !== $currentUser->colegio->nombre)) {
            return back()->with('error', 'No tienes permiso para añadir seguimiento a esta denuncia.');
        }
        // Super Admin puede añadir seguimiento a cualquier denuncia

        // Si la denuncia está "Abierta" y se añade una primera anotación, cambia a "En Trámite"
        $estadoAbierta = DenunciaEstado::where('nombre', 'Abierta')->first();
        $estadoEnTramite = DenunciaEstado::where('nombre', 'En Trámite')->first();

        $oldStatusId = $report->denuncia_estado_id;
        $newStatusId = $report->denuncia_estado_id; // Por defecto, el estado no cambia

        if ($oldStatusId === $estadoAbierta->id) {
            $newStatusId = $estadoEnTramite->id;
            $report->denuncia_estado_id = $newStatusId;
            $report->save();
        }

        // Registrar el seguimiento
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