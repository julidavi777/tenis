<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Inscripciones\DepartamentoMunicipioService;

class JugadorController extends Controller
{
    public function index()
    {
        return view('inscripciones.inicio');
    }

    protected function getListaJugadores()
    {
        /**
         * Relacionar en producción las tablas clubes con jugador
         */
        $jugadores = DB::table('t15_jugadores')
            ->select('c15_jugador_id', 'c15_jugador_fecha_nacimiento', 'c15_jugador_nombres', 'c15_jugador_apellidos', 
                'c15_jugador_genero', 'c.c10_club_nombre AS c15_club_nombre', 'c15_jugador_responsable_id', 
                'p.pais_id AS c15_jugador_pais', 'd.nombre AS c15_jugador_departamento', 'm.nombre AS c15_jugador_municipio')
            ->join('t10_clubes AS c', 'c10_club_id', 'c15_jugador_club_id')
            ->join('paises AS p', 'pais_id', 'c15_jugador_pais_id')
            ->leftJoin('departamentos AS d', 'd.id' , 'c15_jugador_departamento_id')
            ->leftJoin('municipios AS m', 'm.id' , 'c15_jugador_municipio_id')
            ->where('c15_jugador_responsable_id', Auth::id())
            ->orderBy('c15_jugador_apellidos')
            ->get()
            ->toJson();

        return response()->json($jugadores);
    }

    public function getDataJugador(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documento' => 'required|numeric',
            'is_input_form' => 'required|boolean'
        ], 
        [
            'documento.required' => 'El campo es requerido',
            'documento.numeric' => 'El campo debe ser numérico',
            'is_input_form.required' => 'Error ...',
            'is_input_form.boolean' => 'Error ...'
        ]);

        if($validator->fails()){
            return response()->json(
                $validator->errors()
            , 400);
        }

        $validated = $validator->validated();

        $jugador = DB::table('t15_jugadores')
        ->where('c15_jugador_id', $validated['documento'])
        ->first();

        if((int)$validated['is_input_form'] == 1)
        {
            if (!empty($jugador) && !empty($jugador->c15_jugador_responsable_id))
            {
                if ($jugador->c15_jugador_responsable_id !== Auth::id())
                    return response()->json(
                        [
                            'error' => 'El jugador ya se encuentra asignado a un responsable, 
                            contactese con el administrador'
                        ],
                        400
                    );
            }
        }

        return response()->json($jugador);
    }

    protected function asignarJugador(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'documento_anterior' => [ 'nullable', 'numeric' ],
            'documento' => [ 'required', 'numeric', 'digits_between:6,10' ],
            'nombres' => [ 'required', 'string' ],
            'apellidos' => [ 'required', 'string' ],
            'genero' => [ 'required', 'string', 'size:1' ],
            'club' => [ 'required', 'numeric' ],
            'nacionalidad' => [ 'required', 'string' ],
            'fecha_nacimiento' => [ 'required', 'date_format:Y-m-d' ],
            //'pais_residencia' => [ 'required', 'string', 'size:3' ],
            'departamento_residencia' => [ 'required', 'numeric' ],
            'municipio_residencia' => [ 'required', 'numeric' ]
        ]);

        $documento = 0;

        if($validated['documento_anterior'] == $validated['documento'] )
        {
            $campos_insert['c15_jugador_id'] = $validated['documento_anterior'];
            $documento = $validated['documento_anterior'];
        }
        elseif($validated['documento_anterior'] == 0)
        {
            $check_responsable = DB::table('t15_jugadores')
                ->where('c15_jugador_id', $validated['documento'])
                ->whereNotNull('c15_jugador_responsable_id')
                //->where('c15_jugador_responsable_id', Auth::id())
                ->exists();

            if($check_responsable)
            {
                return back()->withErrors([
                    'Error' => 'El jugador ya se encuentra asignado a un responsable, 
                    por favor, contactese con un administrador.'
                ]);
            }

            $campos_insert['c15_jugador_id'] = $validated['documento'];
            $documento = $validated['documento'];
        }
        else
        {
            $check_responsable = DB::table('t15_jugadores')
                ->where('c15_jugador_id', $validated['documento'])
                ->where('c15_jugador_responsable_id', Auth::id())
                ->exists();

            if($check_responsable)
                return back()->withErrors([
                    'Error' => 'El documento ya se encuentra registrado y no es posible cambiarlo,
                        por favor, contactese con un administrador.'
                ]);

            $campos_insert['c15_jugador_id'] = $validated['documento'];
            $documento = $validated['documento_anterior'];
        }

        $campos_insert['c15_jugador_apellidos'] = $validated['apellidos'];
        $campos_insert['c15_jugador_nombres'] = $validated['nombres'];
        $campos_insert['c15_jugador_genero'] = $validated['genero'];
        $campos_insert['c15_jugador_nacionalidad'] = $validated['nacionalidad'];
        $campos_insert['c15_jugador_club_id'] = $validated['club'];
        $campos_insert['c15_jugador_fecha_nacimiento'] = $validated['fecha_nacimiento'];
        $campos_insert['c15_jugador_responsable_id'] = Auth::id();
        $campos_insert['c15_jugador_departamento_id'] = $validated['departamento_residencia'];
        $campos_insert['c15_jugador_municipio_id'] = $validated['municipio_residencia'];
        $campos_insert['c15_jugador_pais_id'] = 'COL';
        //$campos_insert['c15_jugador_pais_id'] = $validated['pais_residencia'];

        DB::table('t15_jugadores')
        ->updateOrInsert(
            [ 'c15_jugador_id' => $documento ],
            $campos_insert
        );

        return back()->with('success_jugador', 'Se ha asignado/actualizado el jugador correctamente');
    }

    protected function getMunicipios($departamento)
    {
        return response()->json(DepartamentoMunicipioService::getMunicipios($departamento));
    }

    protected function deleteJugador(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documento' => 'required|numeric'
        ], 
        [
            'documento.required' => 'El campo es requerido',
            'documento.numeric' => 'El campo debe ser numérico'
        ]);

        if($validator->fails()){
            return response()->json(
                $validator->errors()
            , 400);
        }

        $validated = $validator->validated();

        DB::table('t15_jugadores')
        ->where('c15_jugador_id', $validated['documento'])
        ->update([
            'c15_jugador_responsable_id' => null
        ]);

        return response()->json([
            'message' => 'Eliminado correctamente'
        ]);
    }
}
