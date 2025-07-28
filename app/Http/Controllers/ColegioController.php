<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Municipio;
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
        $municipios = Municipio::where('activo', true)->orderBy('nombre')->get();
        
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
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre del colegio es obligatorio.',
            'nombre.unique' => 'Ya existe un colegio con este nombre.',
            'municipio_id.required' => 'Debes seleccionar un municipio.',
            'municipio_id.exists' => 'El municipio seleccionado no es válido.',
        ]);

        Colegio::create([
            'nombre' => $request->nombre,
            'municipio_id' => $request->municipio_id,
            'activo' => $request->has('activo') ? true : false,
        ]);

        return redirect()->route('colegios.index')->with('success', 'Colegio creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un colegio existente.
     * Accesible solo por Super Administrador.
     */
    public function edit(Colegio $colegio)
    {
        $municipios = Municipio::where('activo', true)->orderBy('nombre')->get();
        
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
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre del colegio es obligatorio.',
            'nombre.unique' => 'Ya existe un colegio con este nombre.',
            'municipio_id.required' => 'Debes seleccionar un municipio.',
            'municipio_id.exists' => 'El municipio seleccionado no es válido.',
        ]);

        $colegio->update([
            'nombre' => $request->nombre,
            'municipio_id' => $request->municipio_id,
            'activo' => $request->has('activo') ? true : false,
        ]);

        return redirect()->route('colegios.index')->with('success', 'Colegio actualizado exitosamente.');
    }

    /**
     * Elimina un colegio de la base de datos.
     * Accesible solo por Super Administrador.
     */
    public function destroy(Colegio $colegio)
    {
        // Verificar si hay usuarios asociados al colegio
        if ($colegio->users()->count() > 0) {
            return redirect()->route('colegios.index')->with('error', 'No se puede eliminar el colegio porque tiene usuarios asociados.');
        }

        $colegio->delete();
        return redirect()->route('colegios.index')->with('success', 'Colegio eliminado exitosamente.');
    }
}