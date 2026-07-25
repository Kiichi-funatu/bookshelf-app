<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\GenreStoreRequest;
use App\Http\Requests\GenreUpdateRequest;

class GenreController extends Controller
{
    // ジャンル一覧
    public function index()
    {
        // 書籍数を含めて取得
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    // ジャンル詳細
    public function show(Genre $genre)
    {
        // ジャンルに紐づく書籍を10件ずつ取得
        $books = $genre->books()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    // ジャンル登録画面
    public function create()
    {
        return view('genres.create');
    }

    // ジャンル登録
    public function store(GenreStoreRequest $request)
    {
        // バリデーション → 登録
        Genre::create($request->validated());

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを登録しました。');
    }

    // ジャンル編集画面
    public function edit(Genre $genre)
    {
        // 編集フォーム表示
        return view('genres.edit', compact('genre'));
    }

    // ジャンル更新
    public function update(GenreUpdateRequest $request, Genre $genre)
    {
        // バリデーション → 更新
        $genre->update($request->validated());

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを更新しました。');
    }

    // ジャンル削除
    public function destroy(Genre $genre)
    {
        // 書籍紐付きチェック
        if ($genre->books()->exists()) {
            return back()->with('error', 'このジャンルには書籍が紐づいているため削除できません。');
        }

        // 紐付きがない場合のみ削除
        $genre->delete();

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを削除しました。');
        }
}
