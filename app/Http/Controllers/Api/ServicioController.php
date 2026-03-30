<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\Establecimiento;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    /**
     * Almacenar un nuevo servicio
     */
    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimiento,id',
            'nombre_servicio' => 'required|string|max:250',
            'descripcion_servicio' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:menu,servicio',
            'icono' => 'required|string|max:70',
            'disponible' => 'boolean'
        ]);

        // Verificar que el usuario autenticado sea el dueño del establecimiento
        $establecimiento = Establecimiento::find($request->establecimiento_id);

        if (!$establecimiento || $establecimiento->user_id !== auth()->id()) {
            return response()->json([
                'error' => 'No tienes permiso para agregar servicios a este establecimiento'
            ], 403);
        }

        try {
            $servicio = Servicio::create([
                'establecimiento_id' => $request->establecimiento_id,
                'nombre_servicio' => $request->nombre_servicio,
                'descripcion_servicio' => $request->descripcion_servicio,
                'precio' => $request->precio,
                'tipo' => $request->tipo,
                'icono' => $request->icono,
                'disponible' => $request->disponible ?? true
            ]);

            return response()->json([
                'message' => 'Servicio creado exitosamente',
                'data' => $servicio
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener servicios de un establecimiento
     */
    public function getByEstablecimiento($establecimientoId)
    {
        $servicios = Servicio::where('establecimiento_id', $establecimientoId)
            ->get();

        return response()->json([
            'data' => $servicios
        ], 200);
    }

    /**
     * Actualizar un servicio
     */
    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json([
                'error' => 'Servicio no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el dueño del establecimiento
        if ($servicio->establecimiento->user_id !== auth()->id()) {
            return response()->json([
                'error' => 'No tienes permiso para actualizar este servicio'
            ], 403);
        }

        $request->validate([
            'nombre_servicio' => 'sometimes|required|string|max:250',
            'descripcion_servicio' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'tipo' => 'sometimes|required|in:menu,servicio',
            'icono' => 'sometimes|required|string|max:70',
            'disponible' => 'boolean'
        ]);

        $servicio->update($request->all());

        return response()->json([
            'message' => 'Servicio actualizado exitosamente',
            'data' => $servicio
        ], 200);
    }

    /**
     * Eliminar un servicio
     */
    public function destroy($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json([
                'error' => 'Servicio no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el dueño del establecimiento
        if ($servicio->establecimiento->user_id !== auth()->id()) {
            return response()->json([
                'error' => 'No tienes permiso para eliminar este servicio'
            ], 403);
        }

        $servicio->delete();

        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ], 200);
    }
}
