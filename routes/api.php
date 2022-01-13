<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AuthController,
    BilletController,
    DocController,
    FondAndLostController,
    ReservationController,
    UnitController,
    UserController,
    WallController,
    WarningController,
};

Route::get('/ping', function () {
    return ['pong' => true];
});