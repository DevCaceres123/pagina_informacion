<?php

namespace App\Http\Requests\Sede;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Validation\Rule;

class SedesRquest extends BasePrincipalRequest
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
            case 'sedes.store':
                return [
                    'nombre' => 'required|max:50|min:5|unique:sedes,nombre',
                    'descripcion' => 'required|max:100|min:5',
                    'resolucion_numero' => 'required|max:100|min:5',
                    'resolucion_archivo' => 'required|mimes:pdf|max:2048',
                    'galeria' => 'nullable|array',
                    'galeria.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072', // 3MB max por imagen
                    'mapa_url' => 'nullable|url|max:255',
                    'whatsapp' => 'nullable|regex:/^\d{8}$/',
                    'facebook' => 'nullable|url|max:255',
                    'youtube' => 'nullable|url|max:255',
                ];
            case 'sede.actualizarDatos':
                return [
                    'nombre_edit' => 'required|max:50|min:5',
                    'descripcion_edit' => 'required|max:100|min:5',
                    'resolucion_numero_edit' => 'required|max:100|min:5',                   
                    'whatsapp_edit' => 'nullable|regex:/^\d{8}$/',
                    'facebook_edit' => 'nullable|url|max:255',
                    'youtube_edit' => 'nullable|url|max:255',
                ];


            default:
                return [];
        }
    }
}
