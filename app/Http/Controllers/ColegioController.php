<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Municipio; // Necesario para el formulario
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColegioController extends Controller
{
    /**
     * Muestra una lista de todos los colegios.
     * Accesible solo por Super Administrador.
     */
    public function index()
    {
        $colegios = Colegio::with('municipio')->latest()->paginate(10);
        return view('admin.colegios.index', compact('colegios'));
    }

    /**
     * Muestra el formulario para crear un nuevo colegio.
     * Accesible solo por Super Administrador.
     */
    public function create()
    {
        $municipios = Municipio::where('activo', true)->get(); // Solo se pueden asignar a municipios activos
        return view('admin.colegios.create', compact('municipios'));
    }

    /**
     * Almacena un nuevo colegio en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:colegios,nombre',
            'municipio_id' => 'required|exists:municipios,id',
            'activo' => 'boolean', // Campo activo es un checkbox
        ]);

        Colegio::create($request->all());
        return redirect()->route('colegios.index')->with('success', 'Colegio creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un colegio existente.
     * Accesible solo por Super Administrador.
     */
    public function edit(Colegio $colegio)
    {
        $municipios = Municipio::where('activo', true)->get(); // Solo se pueden asignar a municipios activos
        return view('admin.colegios.edit', compact('colegio', 'municipios'));
    }

    /**
     * Actualiza un colegio existente en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function update(Request $request, Colegio $colegio)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('colegios')->ignore($colegio->id)],
            'municipio_id' => 'required|exists:municipios,id',
            // La casilla de verificación 'activo' puede no estar presente en el request si no está marcada,
            // por lo que se ajusta a 0 si no se envía.
            'activo' => 'boolean', 
        ]);

        // Si el checkbox 'activo' no está marcado en el formulario, Request no lo incluye.
        // Aseguramos que se guarde como false (0) en la base de datos.
        $data = $request->all();
        $data['activo'] = $request->has('activo'); // Establece a true si está presente, false si no.

        $colegio->update($data);
        return redirect()->route('colegios.index')->with('success', 'Colegio actualizado exitosamente.');
    }

    /**
     * Elimina un colegio de la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function destroy(Colegio $colegio)
    {
        $colegio->delete();
        return redirect()->route('colegios.index')->with('success', 'Colegio eliminado exitosamente.');
    }
}