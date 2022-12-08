<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

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

Route::prefix('v1/')->group(function () {
    Route::post('/upload', [ApiController::class, 'postImage']);
    Route::get('/data', [ApiController::class, 'getDataByPage']);
    Route::get('/data/{id}', [ApiController::class, 'getDataById']);
    Route::get('data/tempimage/{path}', function (string $path){
        return Storage::disk('private')->get('images/'.$path);
    })->name('data.temp');
});
