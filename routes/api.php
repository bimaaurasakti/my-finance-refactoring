<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TypeController;

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



Route::post('/auth/login', [AuthController::class, 'login']) -> name('auth.login');
Route::post('/auth/register', [AuthController::class, 'register']) -> name('auth.register');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return auth()->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']) -> name('auth:logout');

    Route::post('/source', [SourceController::class, 'store']);
    Route::get('/source', [SourceController::class, 'sources']);
    Route::put('/source/{id}', [SourceController::class, 'update']);
    Route::delete('/source/{id}', [SourceController::class, 'destroy']);

    Route::post('/transaction', [TransactionController::class, 'store']);
    Route::get('/transaction', [TransactionController::class, 'transactions']);
    Route::put('/transaction/{id}', [TransactionController::class, 'update']);
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy']);

    Route::post('/type', [TypeController::class, 'store']);
    Route::get('/type', [TypeController::class, 'types']);
    Route::put('/type/{id}', [TypeController::class, 'update']);
    Route::delete('/type/{id}', [TypeController::class, 'destroy']);

    Route::get('/history', [HistoryController::class, 'histories']);
    Route::get('/histories_income_expense', [HistoryController::class, 'histories_income_expense']);
});
