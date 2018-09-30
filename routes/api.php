<?php

use Illuminate\Http\Request;

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

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
*/

Route::post('/etupay/callback', 'EtuPayController@etupayCallback');
Route::get('/dons/status', 'DonController@apiGetAmount');
Route::get('/billets/get', 'BilletController@apiGetBillet');

Route::get('/events', 'EventController@show');
Route::get('/partners', 'PartnerController@show');
Route::get('/guichet/{uuid}/billets', 'GuichetController@ApiGetExport');
