<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo admin y operario pueden crear inspecciones
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('operario');
    }
    
    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'producto_id' => 'required|exists:productos,id',
            'resultado' => 'required|in:aprobado,rechazado,cuarentena',
            'motivo_rechazo' => 'required_if:resultado,rechazado|nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }
    
    public function messages(): array
    {
        return [
            'produccion_id.required' => 'Debe seleccionar un lote de producción',
            'producto_id.required' => 'Debe seleccionar un producto',
            'resultado.required' => 'Debe seleccionar un resultado',
            'motivo_rechazo.required_if' => 'Debe especificar el motivo del rechazo',
        ];
    }
}
