<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Book;

class FavoriteController extends Controller
{
    // お気に入り一覧
    public function index()
    {
        // 未ログイン → /login にリダイレクト（middleware で保証）
        $user = Auth::user();

        // ログインユーザーのお気に入り書籍を10件/ページで取得
        $books = $user->favorites()->paginate(10);

        return view('favorites.index', compact('books'));
    }

    // お気に入りトグル
    public function toggle(Book $book)
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
