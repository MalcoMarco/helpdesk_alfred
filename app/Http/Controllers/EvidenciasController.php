<?php

namespace App\Http\Controllers;

use App\Models\Evidencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
class EvidenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Evidencias/Index', [
            'title' => 'Vault de Evidencias',
            'evidencias' => Evidencias::where('user_id', auth()->id())->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:20480', // 20MB max
        ]);

        $uploadedFiles = [];
        
        try {
            foreach ($request->file('files') as $file) {
                // Generar un nombre único para el archivo
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Guardar el archivo en storage/app/public/evidencias
                $path = $file->storeAs('evidencias', $fileName, 'public');
                
                // Crear registro en la base de datos
                $evidencia = Evidencias::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => '/storage/' . $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'user_id' => auth()->id(),
                ]);
                
                $uploadedFiles[] = $evidencia;
            }
            
            return Redirect::back()->with('success', 'Archivos subidos correctamente');
            
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Error al subir los archivos: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Evidencias $evidencias)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evidencias $evidencias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evidencias $evidencias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evidencias $evidencias)
    {
        //
    }
}
