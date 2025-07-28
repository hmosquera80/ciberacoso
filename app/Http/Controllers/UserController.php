<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colegio; // Necesario para el formulario de usuario (asignar colegio)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules; // Para reglas de contraseña
use Illuminate\Validation\Rule; // Para Rule::in y Rule::unique

class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     * Accesible solo por Super Administrador.
     */
    public function index()
    {
        // Carga los usuarios con su colegio asociado para mostrarlos en la tabla
        $users = User::with('colegio')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Accesible solo por Super Administrador.
     */
    public function create()
    {
        // Pasa todos los colegios a la vista para el desplegable de asignación
        $colegios = Colegio::all();
        return view('admin.users.create', compact('colegios'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function store(Request $request)
    {
        // Reglas de validación para la creación de un usuario
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['super_admin', 'admin', 'supervisor'])], // Valida que el rol sea uno de los permitidos
            'colegio_id' => ['nullable', 'exists:colegios,id'], // El colegio es nullable (para super_admin) y debe existir
        ]);

        // Crear el nuevo usuario
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'colegio_id' => $request->colegio_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * Accesible solo por Super Administrador.
     */
    public function edit(User $user)
    {
        // Pasa el usuario a editar y todos los colegios a la vista
        $colegios = Colegio::all();
        return view('admin.users.edit', compact('user', 'colegios'));
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function update(Request $request, User $user)
    {
        // Reglas de validación para la actualización de un usuario
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)], // Email debe ser único, ignorando el usuario actual
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Contraseña es opcional, si se provee, debe confirmarse y seguir las reglas
            'role' => ['required', 'string', Rule::in(['super_admin', 'admin', 'supervisor'])],
            'colegio_id' => ['nullable', 'exists:colegios,id'],
        ]);

        // Actualizar los campos del usuario
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) { // Si se ha proporcionado una nueva contraseña, encriptarla
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->colegio_id = $request->colegio_id;
        $user->save(); // Guardar los cambios

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function destroy(User $user)
    {
        $user->delete(); // Eliminar el usuario
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Muestra el formulario para crear un nuevo usuario con rol 'supervisor',
     * pre-asignado al colegio del administrador actual.
     * Accesible solo por Administrador.
     */
    public function createSupervisor()
    {
        // Obtiene el colegio asociado al administrador actualmente logueado
        $adminColegio = auth()->user()->colegio; 

        // Si el administrador no tiene un colegio asignado, no puede crear supervisores
        if (!$adminColegio) {
            return redirect()->back()->with('error', 'Tu cuenta de administrador no está asociada a un colegio. No puedes crear supervisores.');
        }
        // Pasa la información del colegio del administrador a la vista
        return view('admin.users.create-supervisor', compact('adminColegio'));
    }

    /**
     * Almacena un nuevo usuario con rol 'supervisor' en la base de datos,
     * asignado al colegio del administrador actual.
     * Accesible solo por Administrador.
     */
    public function storeSupervisor(Request $request)
    {
        // Validar los datos de entrada para el nuevo supervisor
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Obtiene el ID del colegio del administrador logueado
        $adminColegioId = auth()->user()->colegio_id; 

        // Verificar de nuevo que el administrador tenga un colegio asignado
        if (!$adminColegioId) {
             return redirect()->back()->with('error', 'Tu cuenta no puede crear supervisores sin un colegio asignado.');
        }

        // Crear el nuevo usuario supervisor
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supervisor', // El rol se fuerza a 'supervisor'
            'colegio_id' => $adminColegioId, // El colegio se fuerza al colegio del administrador
        ]);

        return redirect()->route('dashboard')->with('success', 'Supervisor creado exitosamente.');
    }
}