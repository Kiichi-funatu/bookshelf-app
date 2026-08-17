<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\GenreStoreRequest;
use App\Http\Requests\GenreUpdateRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class GenreController extends Controller
{
    /**
     * ジャンル一覧を表示する
     *
     * @return View
     */
    public function index(): View
    {
        // 書籍数を含めて取得
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル詳細を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function show(Genre $genre): View
    {
        // ジャンルに紐づく書籍を10件ずつ取得
        $books = $genre->books()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル登録画面を表示する
     *
     * @return View
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * ジャンルを登録する
     *
     * @param GenreStoreRequest $request
     * @return RedirectResponse
     */
    public function store(GenreStoreRequest $request): RedirectResponse
    {
        // バリデーション → 登録
        Genre::create($request->validated());

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを登録しました。');
    }

    /**
     * ジャンル編集画面を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function edit(Genre $genre): View
    {
        // 編集フォーム表示
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンルを更新する
     *
     * @param GenreUpdateRequest $request
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function update(GenreUpdateRequest $request, Genre $genre): RedirectResponse
    {
        // バリデーション → 更新
        $genre->update($request->validated());

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを更新しました。');
    }

    /**
     * ジャンルを削除する
     *
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function destroy(Genre $genre): RedirectResponse
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
