<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReadingPlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Public Routes (ゲストもアクセス可能)
|--------------------------------------------------------------------------
*/
Route::get('/register', [RegisterController::class, 'create'])->name('register');

Route::get('/login', [LoginController::class, 'create'])->name('login');

// トップ（書籍一覧）
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);

// ランキング
Route::get('/ranking', [BookController::class, 'ranking'])->name('ranking.index');

// ISBN検索（応用）
Route::get('/books/isbn/{isbn}', [BookController::class, 'isbnLookup'])->name('books.isbn.lookup');

/*
|--------------------------------------------------------------------------
| 認証必須 (auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |-----------------------------------------
    | 書籍
    |-----------------------------------------
    */
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');

    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    /*
    |-----------------------------------------
    | レビュー
    |-----------------------------------------
    */
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // レビューいいね
    Route::post('/reviews/{review}/like', [ReviewController::class, 'toggleLike'])->name('reviews.like');

    /*
    |-----------------------------------------
    | お気に入り
    |-----------------------------------------
    */
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    /*
    |-----------------------------------------
    | ジャンル
    |-----------------------------------------
    */
    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
    Route::get('/genres/create', [GenreController::class, 'create'])->name('genres.create');
    Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');

    Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');
    Route::get('/genres/{genre}/edit', [GenreController::class, 'edit'])->name('genres.edit');
    Route::put('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');

    /*
    |-----------------------------------------
    | マイ読書レポート
    |-----------------------------------------
    */
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    /*
    |-----------------------------------------
    | 読書計画 (Reading Plans)
    |-----------------------------------------
    */
    Route::get('/reading-plans', [ReadingPlanController::class, 'index'])->name('reading_plans.index');
    Route::get('/reading-plans/create', [ReadingPlanController::class, 'create'])->name('reading_plans.create');
    Route::post('/reading-plans', [ReadingPlanController::class, 'store'])->name('reading_plans.store');

    Route::get('/reading-plans/{plan}/edit', [ReadingPlanController::class, 'edit'])->name('reading_plans.edit');
    Route::put('/reading-plans/{plan}', [ReadingPlanController::class, 'update'])->name('reading_plans.update');
    Route::delete('/reading-plans/{plan}', [ReadingPlanController::class, 'destroy'])->name('reading_plans.destroy');

    // 読了ボタン
    Route::post('/reading-plans/{plan}/complete', [ReadingPlanController::class, 'complete'])->name('reading_plans.complete');

    /*
    |-----------------------------------------
    | 通知
    |-----------------------------------------
    */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// 書籍詳細
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    
