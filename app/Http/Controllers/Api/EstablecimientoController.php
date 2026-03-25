<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEstablecimeintoRequest;


class EstablecimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        $establecimientos = Establecimiento::where('user_id', $userId)->with('categoria')->get();

        return response()->json([
            'data' => $establecimientos
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
    public function store(StoreEstablecimeintoRequest $request)
    {
        // return $request->file('imagen')->storeAs('uploads', $request->file('imagen')->hashName(), 'public');

        if ($request->hasFile('imagen_file')) {
            $file = $request->file('imagen_file');
            $filename = $file->hashName();
            $fullPath = $file->storeAs('uploads', $filename, 'public');
            $request->merge(['imagen' => $fullPath]);
        }
        $establecimiento = Establecimiento::create($request->all());
        return response()->json([
            'message' => 'Establecimiento creado exitosamente',
            'data' => $establecimiento
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $establecimiento = Establecimiento::findOrFail($id);
        $establecimiento->load('categoria');

        return response()->json([
            'data' => $establecimiento
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->method() == 'PATCH') {
            $request->validate([
                'estado' => 'required|in:ACTIVO,INACTIVO'
            ]);

            $establecimiento = Establecimiento::findOrFail($id);
            $establecimiento->estado = $request->input('estado');
            $establecimiento->save();

            return response()->json([
                'message' => 'Estado actualizado exitosamente',
                'data' => $establecimiento
            ]);
        }



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $establecimiento = Establecimiento::findOrFail($id);
        if ($establecimiento->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'No autorizado para eliminar este establecimiento'
            ], 403);
        }

        if ($establecimiento->imagen) {
            Storage::disk('public')->delete($establecimiento->imagen);
        }
        $establecimiento->delete();

        return response()->json([
            'message' => 'Establecimiento eliminado exitosamente'
        ]);
    }

    public function establecimientosPublic(Request $request)
    {
        $search = $request->input('search');
        $search = str_ireplace(' ', '%', $search);

        $query = Establecimiento::query()->with('categoria');

        $ordenarPor = $request->input('sort');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('descripcion', 'like', "%$search%")
                    ->orWhere('direccion', 'like', "%$search%");
            });
        }

        if ($request->input('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        if ($ordenarPor === 'nuevo') {
            $query->orderBy('created_at', 'desc');
        } elseif ($ordenarPor === 'antiguo') {
            $query->orderBy('created_at', 'asc');
        } elseif ($ordenarPor === 'az') {
            $query->orderBy('nombre', 'asc');
        } elseif ($ordenarPor === 'za') {
            $query->orderBy('nombre', 'desc');
        }

        $query->where('estado', 'ACTIVO');
        return $query
            ->paginate($request->input('per_page', 10), ['*'], 'page', $request->input('page', 1));
    }

    public function establecimientoIdPublic(string $id)
    {
        $establecimiento = Establecimiento::findOrFail($id);
        if ($establecimiento->estado !== 'ACTIVO') {
            return response()->json([
                'message' => 'Establecimiento no disponible'
            ], 404);
        }
        $establecimiento->load('categoria');

        return response()->json([
            'data' => $establecimiento
        ]);
    }
}
