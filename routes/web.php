<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GruposRoundRobinController;
use App\Http\Controllers\CuadrosPrincipalesController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\InscripcionesController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PruebaController; 

Route::get('prueba', [PruebaController::class, 'index']);

Route::middleware('preventHistory')->group(function () {
    Route::get('/', [GruposRoundRobinController::class, 'index'])
        ->name('dashboard');

    Route::post('/', [GruposRoundRobinController::class, 'getGruposRoundRobin'])
        ->name('dashboard');

    Route::get('/cuadros-principales', [CuadrosPrincipalesController::class, 'index'])
        ->name('cuadros.principales');

    Route::post('/cuadros-principales', [CuadrosPrincipalesController::class, 'getCuadrosPrincipales'])
        ->name('cuadros.principales');

    Route::get('change-torneo/{id}', function(int $id)  
    {
        try
        {
            $user = Auth::user();
            $user->torneo_id = $id;

            // Si el Editor de código le muestra que hay un error, no lo es,
            // ya que la variable Auth retorna un modelo Eloquent 
            // donde el método $user->save() está, solo que el Editor de código 
            // por alguna razón muestra error. 
            //
            // Y si no le muestra nada mano mejor.
            $user->save();
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Ha ocurrido un error inesperado',
            ], 400);
        }

        return response()->json([
            'success' => 'ok'
        ], 200);

    })->name('torneo.change');

    Route::middleware(['auth', 'verified'])->group(function () {

        Route::prefix('/administrar-jugadores')->group(function () {
    
            Route::get('/', [JugadorController::class, 'index'])
                ->name('inscripciones.inicio');
    
            Route::get('lista-jugadores', [JugadorController::class, 'getListaJugadores'])
                ->name('inscripciones.lista.jugadores');
    
            Route::get('datos-jugador', [JugadorController::class, 'getDataJugador'])
                ->name('inscripciones.data.jugador');
    
            Route::get('municipios/{departamento}', [JugadorController::class, 'getMunicipios'])
                ->name('inscripciones.municipios.jugador');
            
            Route::get('eliminar', [JugadorController::class, 'deleteJugador'])
                ->name('jugador.eliminar');
    
            Route::post('asignar', [JugadorController::class, 'asignarJugador'])
                ->name('inscripciones.asignar.jugador');
        });
    
        Route::prefix('/inscripciones')->group(function () {

            Route::get('/', [InscripcionesController::class, 'index'])
            ->name('inscripciones.registro');

            Route::post('/registro', [InscripcionesController::class, 'inscribir'])
            ->name('inscripciones.inscripcion');

            Route::get('/delete', [ InscripcionesController::class, 'eliminar'])
            ->name('inscripciones.delete');

            Route::post('/pago', [ InscripcionesController::class, 'setPago'])
            ->name('inscripciones.pago');
        });
    });

    require __DIR__ . '/auth.php';
});
