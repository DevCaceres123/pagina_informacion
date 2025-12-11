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
                    'solicitud' => 'required|file|mimes:pdf|max:5120', // 5MB max
                    'planos' => 'required|array',
                    'planos.*' => 'image|mimes:jpeg,png,jpg,jpeg,webp|max:5120', // 5MB
                ];
            case 'infraestructura.guardarDatosUbicacion':
                return [
                    'infraestructura_id' => 'required|exists:infraestructuras,id',
                    'distrito' => 'nullable|integer|digits_between:1,3',
                    'ubicacion' => 'nullable|string|min:5|max:255',
                    'urb'       => 'nullable|string|min:3|max:255',
                    'manzano' => 'nullable|integer|digits_between:1,2',
                    'lote' => 'nullable|integer|digits_between:1,2',
                    'sup_test' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'sup_lev' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'sup_adju' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'sup_util' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'escala' => 'nullable|string|min:3|max:20',
                ];
            case 'infraestructura.agregarImagenesPlanos':
                return [
                    'nuevasImagenes' => 'required|array',
                    'nuevasImagenes.*' => 'image|mimes:jpeg,png,jpg,jpeg,webp|max:5120', // 5MB   
                ];
            
            default:
                return [];
        }
    }
}
