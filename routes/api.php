<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('files', [FileController::class, 'index'])->name('file.index');
Route::post('file/upload', [FileController::class, 'upload'])->name('file.upload');
Route::delete('files/delete', [FileController::class, 'destory'])->name('file.delete');
Route::post('file/check_exists', [FileController::class, 'checkExists'])->name('file.check_exists');
