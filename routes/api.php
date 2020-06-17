<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('cars', 'FuelsApi@cars');

Route::get('fuels', 'FuelsApi@fuels');

Route::get('get-distance-between-points', 'FuelsApi@distance');

Route::get('get-car-fuel-info', 'FuelsApi@carFuelInfo');
