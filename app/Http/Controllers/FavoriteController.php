<?php

namespace App\Http\Controllers;

use App\Models\Book;

class FavoriteController extends Controller
{
    // お気に入り一覧
    public function index()
    {
        // ログインユーザーのお気に入り書籍一覧
    }

    // お気に入りトグル
    public function toggle(Book $book)
    {
        // 追加 or 削除
    }
}
