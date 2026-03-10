<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Establecimiento;
use Illuminate\Support\Facades\DB;

class EstablecimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $search = str_ireplace(' ', '%', $search);

        $query = Establecimiento::query()->with('categoria');

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

        return $query
            ->paginate($request->input('per_page', 10), ['*'], 'page', $request->input('page', 1));

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
