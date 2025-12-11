<?php

namespace App\Http\Requests\Noticia;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;

class NoticiaRequest extends BasePrincipalRequest
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
            case 'noticia.store':
                return [
                    'titulo' => 'required|min:5|max:100',
                    'contenido' => 'required|min:10|max:5000',
                    'sede'=>'required|integer|exists:sedes,id',
                    'tipo'=>'required|integer|exists:categorias_noticias,id',                    
                    'portada' => 'required|image|mimes:jpeg,png,jpg,jpeg,webp|max:5120', // 5MB
                    'fotos' => 'array',
                    'fotos.*' => 'image|mimes:jpeg,png,jpg,jpeg,webp|max:5120', // 5MB
                ];
                        
            default:
                return [];
        }
    }
}
