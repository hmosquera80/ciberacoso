<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colegio; // Necesario para el formulario de usuario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule; // Importar Rule para validación unique/in

class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     * Accesible solo por Super Administrador.
     */
    public function index()
    {
        $users = User::with('colegio')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Accesible solo por Super Administrador.
     */
    public function create()
    {
        $colegios = Colegio::all(); // Necesario para seleccionar colegio al crear usuario
        return view('admin.users.create', compact('colegios'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['super_admin', 'admin', 'supervisor'])],
            'colegio_id' => ['nullable', 'exists:colegios,id'],
        ]);

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
        $colegios = Colegio::all();
        return view('admin.users.edit', compact('user', 'colegios'));
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['super_admin', 'admin', 'supervisor'])],
            'colegio_id' => ['nullable', 'exists:colegios,id'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) { // Solo actualizar la contraseña si se proporciona una nueva
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->colegio_id = $request->colegio_id;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Muestra el formulario para crear un nuevo usuario con rol 'supervisor',
     * pre-asignado al colegio del administrador actual.
     * Accesible solo por Administrador.
     */
    public function createSupervisor()
    {
        // Un administrador solo puede crear supervisores en SU colegio
        $adminColegio = auth()->user()->colegio; // Obtiene el colegio del admin logueado
        if (!$adminColegio) {
            // Redirige si el administrador no tiene un colegio asociado (necesario para crear supervisor)
            return redirect()->back()->with('error', 'Tu cuenta de administrador no está asociada a un colegio. No puedes crear supervisores.');
        }
        return view('admin.users.create-supervisor', compact('adminColegio'));
    }

    /**
     * Almacena un nuevo usuario con rol 'supervisor' en la base de datos,
     * asignado al colegio del administrador actual.
     * Accesible solo por Administrador.
     */
    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $adminColegioId = auth()->user()->colegio_id; // Obtiene el ID del colegio del admin
        if (!$adminColegioId) {
             // Redirige si el administrador no tiene colegio asignado al momento de guardar
             return redirect()->back()->with('error', 'Tu cuenta no puede crear supervisores sin un colegio asignado.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supervisor', // El rol se fuerza a 'supervisor'
            'colegio_id' => $adminColegioId, // El colegio se fuerza al colegio del admin
        ]);

        return redirect()->route('dashboard')->with('success', 'Supervisor creado exitosamente.');
    }
}