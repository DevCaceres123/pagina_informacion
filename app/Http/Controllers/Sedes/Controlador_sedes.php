<?php

namespace App\Http\Controllers\Sedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\ImgSede;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Sede\SedesRquest;
use Exception;

class Controlador_sedes extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrador.sedes.sedes');
    }

    public function listarSedes(Request $request)
    {
        $query = Sede::with([
            'carreras' => function ($query) {
                $query->select(['nombre', 'sede_id']); // CORREGIDO
            },
        ])->select('id', 'nombre', 'descripcion', 'resolucion', 'resolucion_pdf', 'estado')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')->orWhere('resolucion', 'like', '%' . $request->search['value'] . '%');
            });
        }

        // Total de registros antes del filtrado
        $recordsTotal = $query->count();

        // Paginación y orden
        $sedes = $query->skip($request->start)->take($request->length)->get();

        // Respuesta
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal, // Ajustar si hay filtros
            'data' => $sedes,
            'permisos' => [
                'editar' => auth()->user()->can('afiliado.editar'),
                'eliminar' => true,
                'estado' => auth()->user()->can('afiliado.estado'),
            ],
        ]);
    }


    public function listarImagenes($id_sede){

        
        $imagenes = ImgSede::select('id', 'imagen')
            ->where('sede_id', $id_sede)
            ->get();


        if ($imagenes->isEmpty()) {
            $this->mensaje('error', 'No hay imágenes para esta sede');
            return response()->json($this->mensaje, 200);
        }

       $this->mensaje('exito', $imagenes);
        return response()->json($this->mensaje, 200);
    }

    // public function agregarImagenes(Request $request)
    // {
        

    //     DB::beginTransaction();
    //     $archivosGuardados = [];

    //     try {
    //         foreach ($request->file('nuevasImagenes') as $imagen) {
    //             $imgSede = new ImgSede();
    //             $imgSede->descripcion = $request->id_sede; // Puedes cambiar esto según tu lógica
    //             $imgSede->sede_id = $request->id_sede;

    //             // Procesar la imagen
    //             $manager = new ImageManager(new Driver());
    //             $img = $manager->read($imagen->getPathname());
    //             $img->resize(height: 800);
    //             $encoded = $img->toJpg(80);

    //             // Generar nombre único
    //             $nombre = uniqid() . '.webp';
    //             $ruta = storage_path("app/public/galeria_sedes/{$nombre}");
    //             $encoded->save($ruta);

    //             // Guardar la ruta relativa para la BD
    //             $imgSede->imagen = str_replace('app/public/galeria_sedes/', '', $ruta);
    //             $imgSede->save();

    //             $archivosGuardados[] = $imgSede->imagen; // guardar para rollback
    //         }

    //         DB::commit();
    //         $this->mensaje('exito', 'Imágenes agregadas correctamente');
    //         return response()->json($this->mensaje, 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Eliminar archivos si ocurre error
    //         foreach ($archivosGuardados as $ruta) {
    //             if (Storage::exists($ruta)) {
    //                 Storage::delete($ruta);
    //             }
    //         }

    //         $this->mensaje('error', 'Error al agregar imágenes: ' . $e->getMessage());
    //         return response()->json($this->mensaje, 200);
    //     }
    // }

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
    public function store(SedesRquest $request)
    {
        DB::beginTransaction();

        try {
            // Guardar la sede
            $sede = new Sede();
            $sede->nombre = $request->nombre;
            $sede->descripcion = $request->descripcion;
            $sede->resolucion = $request->resolucion_numero;
            // $sede->mapa_url = $request->mapa_url;
            $sede->whatsapp = $request->whatsapp;
            $sede->facebook = $request->facebook;
            $sede->youtobe = $request->youtube;
            $sede->estado = 'activo';
            $sede->usuario_id = auth()->user()->id;

            // Guardar el PDF si se envió
            $rutaPdf = $this->guardarPdf($request);
            if ($rutaPdf) {
                $sede->resolucion_pdf = $rutaPdf;
                $archivosGuardados[] = $rutaPdf; // guardar para rollback
            }

            $sede->save();

            // Guardar la galería de imágenes si se envió
            $rutasGaleria = $this->guardarGaleria($request);
            if (!empty($rutasGaleria)) {
                foreach ($rutasGaleria as $ruta) {
                    $imgSede = new ImgSede();
                    $imgSede->descripcion = $request->nombre;
                    $imgSede->imagen = $ruta; // ruta relativa
                    $imgSede->sede_id = $sede->id; // ID de la sede recién creada
                    $imgSede->save();

                    $archivosGuardados[] = $ruta; // guardar para rollback
                }
            }

            DB::commit();

            $this->mensaje('exito', 'Sede registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar archivos si ocurre error
            foreach ($archivosGuardados as $ruta) {
                if (Storage::exists($ruta)) {
                    Storage::delete($ruta);
                }
            }

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // guardamos el pdf o resolucion
    public function guardarPdf(Request $request)
    {
        if ($request->hasFile('resolucion_archivo')) {
            $archivo = $request->file('resolucion_archivo');
            $ruta = $archivo->store('resoluciones', 'public'); // se guarda en storage/app/public/resoluciones
            return str_replace('resoluciones/', '', $ruta); // devuelve: resoluciones/archivo.pdf
        }
        return null;
    }

    // guardarmos las imagenes
    public function guardarGaleria(Request $request)
    {
        $rutas = []; // Para almacenar las rutas de las imágenes guardadas

        if ($request->hasFile('galeria')) {
            // Inicializar el gestor de imágenes con el driver GD
            $manager = new ImageManager(new Driver());

            foreach ($request->file('galeria') as $imagen) {
                // Leer la imagen desde el archivo subido
                $img = $manager->read($imagen->getPathname());

                // Redimensionar manteniendo proporción con altura de 300px
                $img->resize(height: 800);

                // Convertir a JPG con 80% de calidad
                $encoded = $img->toJpg(80);

                // Generar nombre único
                $nombre = uniqid() . '.webp';

                // Ruta absoluta donde guardar
                $ruta = storage_path("app/public/galeria_sedes/{$nombre}");

                // Guardar la imagen procesada
                $encoded->save($ruta);

                $ruta = str_replace('app/public/galeria_sedes/', '', $ruta);

                // Guardar la ruta relativa para la BD
                $rutas[] = $nombre;
            }
        }

        return $rutas; // Devuelve un array con las rutas para guardar en BD si deseas
    }

    public function actualizar_pdf(Request $request, $id)
    {

        $request->validate([
            'nuevoPdf' => 'required|file|mimes:pdf|max:3072', // 3MB
        ]);

        DB::beginTransaction();

        try {
            $sede = Sede::findOrFail($id);

            if ($request->hasFile('nuevoPdf')) {
                // Opcional: eliminar archivo anterior si deseas
                if ($sede->resolucion_pdf) {
                    Storage::disk('public')->delete('resoluciones/' . $sede->resolucion_pdf);
                }

                $archivo = $request->file('nuevoPdf');
                $ruta = $archivo->store('resoluciones', 'public');
                $sede->resolucion_pdf = str_replace('resoluciones/', '', $ruta); //devuelve solo el nombre del archivo
                $sede->save();
            }

            DB::commit();

            $this->mensaje('exito', 'Resolucion actualizada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $sede = Sede::findOrFail($request->id_afiliado);
            if (!$sede) {
                throw new Exception('Afiliado no encontrado');
            }
            if ($request->estado == "activo") {
                $sede->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $sede->estado = "activo";
            }


            $sede->save();
            DB::commit();

            $this->mensaje("exito", "Estado cambiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $sede = Sede::find($id);
            if (!$sede) {
                throw new Exception('Afiliado no encontrado');
            }

            $sede->delete();

            DB::commit();

            $this->mensaje("exito", "Sede eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
