<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class FavoriteController extends Controller
{
    /**
     * お気に入り一覧
     *
     * ログインユーザーのお気に入り書籍を 10 件ずつ表示する。
     *
     * @return View
     */
    public function index(): View
    {
        // 未ログイン → /login にリダイレクト（middleware で保証）
        $user = Auth::user();

        // ログインユーザーのお気に入り書籍を10件/ページで取得
        $books = $user->favorites()->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * お気に入りトグル（追加／解除）
     *
     * すでにお気に入りなら解除、未登録なら追加する。
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function toggle(Book $book): RedirectResponse
    {
        $user = auth()->user();

        if ($user->favoriteBooks->contains($book->id)) {
            // お気に入り解除
            $user->favoriteBooks()->detach($book->id);
        } else {
            // お気に入り追加
            $user->favoriteBooks()->attach($book->id);
        }

        return back();
    }
}
