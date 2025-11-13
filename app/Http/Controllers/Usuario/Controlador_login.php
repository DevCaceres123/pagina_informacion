<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\UsuarioRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\EstadisticaTitulado;
use App\Models\EstadisticaEstudiante;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Controlador_login extends Controller
{
    /**
     * @version 1.0
     * @author  Rodrigo Lecoña Quispe <rodrigolecona97@gmail.com>
     * @param Controlador Administrar la parte de usuario resgistrados LOGIN
     * ¡Muchas gracias por preferirnos! Esperamos poder servirte nuevamente
     */


    /**
     * PARA EL INGRESO DEL USUARIO POR USUARIO Y CONTRASEÑA
     */
    private $mensajeError = "Usuario o contraseña inválidos";

    public function ingresar(Request $request)
    {
        if ($this->validarDatos($request)->fails()) {
            return $this->respuestaError('Todos los campos son requeridos');
        }

        $usuario = $this->buscarUsuario($request->usuario);

        if (!$usuario) {
            return $this->respuestaError($this->mensajeError);
        }

        if ($this->autenticarUsuario($request)) {
            return $this->respuestaExitosa('Inicio de sesión con éxito');
        }

        return $this->respuestaError($this->mensajeError);
    }

    private function validarDatos(Request $request)
    {
        return Validator::make($request->all(), [
            'usuario' => 'required',
            'password' => 'required'
        ]);
    }

    private function buscarUsuario($usuario)
    {
        return User::where('usuario', $usuario)->first();
    }

    private function autenticarUsuario(Request $request)
    {
        $credenciales = [
            'usuario' => $request->usuario,
            'password' => $request->password,
            'estado' => 'activo',
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    private function respuestaExitosa($mensaje)
    {
        return response()->json(mensaje_mostrar('success', $mensaje));
    }

    private function respuestaError($mensaje)
    {
        return response()->json(mensaje_mostrar('error', $mensaje));
    }
    /**
     * FIN PARA EL INGRESO DEL USUARIO Y CONTRASEÑA
     */

    /**
     * PARA INGRESAR AL INICIO
     */
    public function inicio()
    {
        $gestionActual = date('Y');
        $data['menu']   = 0;
        //$data['usuario_estacion'] = User::with(['estacion'])->find(Auth::user()->id);

        // SECCION PARA LA TABLA COMPARATIVA DE TITULADOS POR FECHA DE COLACIÓN
        $estadisticas_por_fecha = EstadisticaTitulado::select(
            DB::raw('DATE(fecha_colacion) as fecha_colacion'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('fecha_colacion', $gestionActual)
            ->groupBy(DB::raw('DATE(fecha_colacion)'))
            ->orderBy('fecha_colacion')
            ->get()
            ->map(function ($item) {
                $item->mes = Carbon::parse($item->fecha_colacion)
                    ->locale('es')
                    ->translatedFormat('F'); // Nombre del mes en español
                return $item;
            });

        $data['fechas_colacion'] = $estadisticas_por_fecha->pluck('mes'); // ["mayo", "enero", ...]
        $data['totales'] = $estadisticas_por_fecha->pluck('total'); // [6000, 5000, ...]

        $crecimientoAnios=$this->crecimientoEstudiantesAnio();
        $data['anios_crecimiento'] = $crecimientoAnios->pluck('gestion');
        $data['totales_crecimiento'] = $crecimientoAnios->pluck('total');

        return view('inicio', $data);
    }

    public function crecimientoEstudiantesAnio()
    {

        $estadisticas_por_fecha = EstadisticaEstudiante::select(
            'gestion',
            DB::raw('SUM(total) as total')
        )
        ->groupBy('gestion')
        ->orderBy('gestion')
        ->get();


        return $estadisticas_por_fecha;

    }



    /**
     * FIN PARA INGRESAR AL INICIO
     */

    /**
     * CERRAR LA SESSIÓN
     */
    public function cerrar_session(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $data = mensaje_mostrar('success', 'Finalizó la session con éxito!');
        return response()->json($data);
    }
    /**
     * FIN DE CERRAR LA SESSIÓN
     */
}
