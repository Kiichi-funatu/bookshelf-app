<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\Api\V1\BookResource;
use App\Http\Resources\Api\V1\BookDetailResource;

class BookApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()
            ->with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // キーワード検索（タイトル・著者）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        // ジャンル絞り込み
        if ($request->filled('genre_id')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        // ページネーション（10件）
        $books = $query->paginate(10);

        return BookResource::collection($books);
    }

   public function show($bookId)
    {
        $book = Book::with([
            'genres',
            'reviews.user',   // 投稿者名を取得するため
        ])->find($bookId);

        if (!$book) {
            return response()->json([
                'message' => '指定された書籍は存在しません。',
            ], 404);
        }

        return new BookDetailResource($book);
    }

}
