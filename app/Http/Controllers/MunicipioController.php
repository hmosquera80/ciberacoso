<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    /**
     * Muestra una lista de todos los municipios.
     * Accesible solo por Super Administrador.
     */
    public function index()
    {
        $municipios = Municipio::latest()->paginate(10);
        return view('admin.municipios.index', compact('municipios'));
    }

    /**
     * Muestra el formulario para crear un nuevo municipio.
     * Accesible solo por Super Administrador.
     */
    public function create()
    {
        return view('admin.municipios.create');
    }

    /**
     * Almacena un nuevo municipio en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:municipios,nombre',
            'activo' => 'boolean', // Campo activo es un checkbox, su valor será 0 o 1
        ]);

        Municipio::create($request->all());
        return redirect()->route('municipios.index')->with('success', 'Municipio creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un municipio existente.
     * Accesible solo por Super Administrador.
     */
    public function edit(Municipio $municipio)
    {
        return view('admin.municipios.edit', compact('municipio'));
    }

    /**
     * Actualiza un municipio existente en la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function update(Request $request, Municipio $municipio)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('municipios')->ignore($municipio->id)],
            // La casilla de verificación 'activo' puede no estar presente en el request si no está marcada,
            // por lo que se ajusta a 0 si no se envía.
            'activo' => 'boolean', 
        ]);

        // Si el checkbox 'activo' no está marcado en el formulario, Request no lo incluye.
        // Aseguramos que se guarde como false (0) en la base de datos.
        $data = $request->all();
        $data['activo'] = $request->has('activo'); // Establece a true si está presente, false si no.

        $municipio->update($data);
        return redirect()->route('municipios.index')->with('success', 'Municipio actualizado exitosamente.');
    }

    /**
     * Elimina un municipio de la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return redirect()->route('municipios.index')->with('success', 'Municipio eliminado exitosamente.');
    }
}