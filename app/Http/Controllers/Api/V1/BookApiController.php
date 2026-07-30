<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\Api\V1\BookResource;
use App\Http\Resources\Api\V1\BookDetailResource;
use App\Http\Requests\Api\V1\StoreBookApiRequest;
use App\Http\Requests\Api\V1\UpdateBookApiRequest;
use App\Http\Requests\Api\V1\SearchBooksApiRequest;

class BookApiController extends Controller
{
    public function index(SearchBooksApiRequest $request)
    {
        $validated = $request->validated();

        $query = Book::query()
            ->with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // キーワード検索
        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        // ジャンル絞り込み
        if (!empty($validated['genre_id'])) {
            $query->whereHas('genres', function ($q) use ($validated) {
                $q->where('genres.id', $validated['genre_id']);
            });
        }

        // ページネーション
        $perPage = $validated['per_page'] ?? 10;

        $books = $query->paginate($perPage);

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

    public function store(StoreBookApiRequest $request)
    {
        $validated = $request->validated();

        // 書籍登録
        $book = Book::create([
            'title'        => $validated['title'],
            'author'       => $validated['author'],
            'isbn'         => $validated['isbn'],
            'published_date' => $validated['published_date'] ?? null,
            'user_id'        => auth()->id() ?? 1,
        ]);

        // ジャンル紐付け（多対多）
        $book->genres()->sync($validated['genre_ids']);

        // 成功時は 201 Created
        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateBookApiRequest $request, $bookId)
    {
        $book = Book::find($bookId);
    
        // 存在チェック（Route Model Binding で自動）
        if (!$book) {
            return response()->json([
                'message' => '指定された書籍は存在しません。',
            ], 404);
        }

        $validated = $request->validated();

        // 書籍更新
        $book->update([
            'title'          => $validated['title'],
            'author'         => $validated['author'],
            'isbn'           => $validated['isbn'],
            'published_date' => $validated['published_date'] ?? null,
        ]);

        // ジャンル更新
        $book->genres()->sync($validated['genre_ids']);

        return new BookResource($book); // 200 OK
    }

    public function destroy($bookId)
    {
        $book = Book::find($bookId);

        // 存在チェック（Route Model Binding）
        if (!$book) {
            return response()->json([
                'message' => '指定された書籍は存在しません。',
            ], 404);
        }

        // 書籍削除（関連データは外部キーの ON DELETE CASCADE で自動削除）
        $book->delete();

        return response()->json([
            'message' => '書籍を削除しました。',
        ], 200);
    }

}
