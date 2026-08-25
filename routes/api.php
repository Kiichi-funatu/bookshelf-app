<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->group(function () {
    
    // 公開API（閲覧のみ）
    Route::get('/books', [BookApiController::class, 'index']);
    Route::get('/books/{book}', [BookApiController::class, 'show']);

    // 認証必須API（作成・編集・削除）
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books', [BookApiController::class, 'store']);
        Route::put('/books/{book}', [BookApiController::class, 'update']);
        Route::delete('/books/{book}', [BookApiController::class, 'destroy']);
    });
});

// ログインユーザー情報（認証必須）
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/external-books', [ApiBookController::class, 'index']);
