<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Book;

class ReviewController extends Controller
{
    // レビュー投稿
    public function store(Request $request, Book $book)
    {
        // バリデーション → レビュー作成
    }

    // レビュー編集画面
    public function edit(Review $review)
    {
        // 認可 → 編集フォーム表示
    }

    // レビュー更新
    public function update(Request $request, Review $review)
    {
        // 認可 → バリデーション → 更新
    }

    // レビュー削除
    public function destroy(Review $review)
    {
        // 認可 → 削除 → いいねも削除
    }

    // レビューいいねトグル
    public function toggleLike(Review $review)
    {
        // 認証 → いいね追加/解除
    }
}
