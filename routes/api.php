<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use relacionado todos os controllers feitos
use App\Http\Controllers\{
    AuthController,
    BilletController,
    DocController,
    FoundAndLostController,
    ReservationController,
    UnitController,
    UserController,
    WallController,
    WarningController,
};

Route::get('/ping', function () {
    return ['pong' => true];
});


//quando não tiver o token,ou não tem acesso é enviado para a pagina login
Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);



Route::middleware('auth:api')->group(function () {
    Route::post('auth/validate', [AuthController::class, 'validateToken']);
    Route::post('auth/logout', [AuthController::class, 'logout']);



    //mural de avisos (walls)
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/wall/{id}/like', [WallController::class, 'like']);


    //documents
    Route::get('/docs', [DocController::class, 'getAll']);


    //livro de ocorrencia
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarning']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);


    //boletos
    Route::get('/billets', [BilletController::class, 'getAll']);


    // achados e perdidos
    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);



    //Unidade
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);


    //reservas
    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);

    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::delete('/myreservation/{id}', [ReservationController::class, 'delmyReservations']);
});
