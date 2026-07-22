<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Book;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends Controller
{
    // レビュー投稿
    public function store(StoreReviewRequest $request, Book $book)
    {
        // 未ログインならリダイレクト（仕様書どおり）
        $this->authorize('create', Review::class);

        $validated = $request->validated();

        $review = $book->reviews()->create([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'レビューを投稿しました');
    }

    // レビュー編集画面
    public function edit(Review $review)
    {
        // 認可（投稿者本人のみ）
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    // レビュー更新
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validated();

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('books.show', $review->book_id)
            ->with('success', 'レビューを更新しました');
    }

    // レビュー削除
    public function destroy(Review $review)
    {
        // 認可（投稿者本人のみ）
        $this->authorize('delete', $review);

        // ① いいね（pivot）を削除
        $review->likedByUsers()->detach();

        // ② レビュー本体を削除
        $review->delete();

        return redirect()->route('books.show', $review->book_id)
            ->with('success', 'レビューを削除しました');
    }

    // レビューいいねトグル
    public function toggleLike(Review $review)
    {
        // 認証必須（middleware('auth') で保証済み）
        $user = auth()->user();

        // すでにいいねしているか？
        if ($user->likedReviews->contains($review->id)) {
            // いいね解除
            $user->likedReviews()->detach($review->id);
        } else {
            // いいね追加
            $user->likedReviews()->attach($review->id);
        }

        return back();
    }
}
