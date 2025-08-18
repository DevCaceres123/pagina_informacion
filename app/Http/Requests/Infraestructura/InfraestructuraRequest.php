<?php

namespace App\Http\Requests\Infraestructura;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;
class InfraestructuraRequest extends BasePrincipalRequest
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
        $routeName = $this->route()->getName();

        switch ($routeName) {
            case 'infraestructura.store':
                return [
                    'propiedad' => 'required|min:3|max:100',
                    'uso_asignado' => 'required|max:100|min:5',
                    'estado_inmueble' => 'required|in:bueno,regular,malo',      
                    'observacion_estado' => 'required|min:5|max:255',                           
                    'sede_id' => 'required|exists:sedes,id',
                    'solicitud' => 'required|file|mimes:pdf|max:3072', // 2MB max
                    'planos' => 'required|array',
                    'planos.*' => 'image|mimes:jpeg,png,jpg,jpeg,webp|max:3072', // 2MB
                ];        
            default:
                return [];
        }
    }
}
