<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    // ジャンル一覧
    public function index()
    {
        // 書籍数付きで一覧表示
    }

    // ジャンル詳細
    public function show(Genre $genre)
    {
        // ジャンルに紐づく書籍一覧（ページネーション）
    }

    // ジャンル登録画面
    public function create()
    {
        return view('genres.create');
    }

    // ジャンル登録
    public function store(Request $request)
    {
        // バリデーション → 登録
    }

    // ジャンル編集画面
    public function edit(Genre $genre)
    {
        // 編集フォーム表示
    }

    // ジャンル更新
    public function update(Request $request, Genre $genre)
    {
        // バリデーション → 更新
    }

    // ジャンル削除
    public function destroy(Genre $genre)
    {
        // 書籍紐付きなら削除不可
    }
}
