<?php

namespace App\Http\Requests\Carrera;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Validation\Rule;

class CarrerasRequest extends BasePrincipalRequest
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
            case 'carrera.store':
                return [
                    'sede_id' => 'required|exists:sedes,id',
                    'nombre' => 'required|max:100|min:5',
                    'modalidad' => 'required|in:semestral,anual',      
                    'malla_curricular' => 'required|mimes:pdf|max:3072',       
                    'vinculo_web' => 'nullable|url|max:255',
                ];        
            default:
                return [];
        }
    }
}
