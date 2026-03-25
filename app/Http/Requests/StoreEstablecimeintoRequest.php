<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstablecimeintoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string',
                'direccion' => 'sometimes|required|string|max:255',
                'telefono' => 'sometimes|nullable|string|max:20',
                'email' => 'sometimes|nullable|email',
                'horario_apertura' => 'sometimes|required|date_format:H:i',
                'horario_cierre' => 'sometimes|required|date_format:H:i|after:horario_apertura',
                'latitud' => 'sometimes|required|numeric',
                'longitud' => 'sometimes|required|numeric',
                'categoria_id' => 'sometimes|required|exists:categoria,id',
                'imagen_file' => 'sometimes|required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'estado' => 'sometimes|required|in:ACTIVO,INACTIVO',
            ];
        }


        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'horario_apertura' => 'required|date_format:H:i',
            'horario_cierre' => 'required|date_format:H:i|after:horario_apertura',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'categoria_id' => 'required|exists:categoria,id',
            'imagen_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'estado' => 'required|in:ACTIVO,INACTIVO',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'El email ya está registrado para otro establecimiento.',
            'horario_cierre.after' => 'El horario de cierre debe ser posterior al horario de apertura.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'imagen_file.image' => 'El archivo debe ser una imagen.',
            'imagen_file.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, webp.',
            'imagen_file.max' => 'La imagen no debe superar los 2MB.',
            'latitud.required' => 'Debe proporcionar una ubicación válida.',
            'longitud.required' => 'Debe proporcionar una ubicación válida.',
        ];
    }
}
