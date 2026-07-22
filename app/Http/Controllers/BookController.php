<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ReviewLike;
use App\Models\Genre;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    // 書籍一覧（検索・絞り込み・ソート・ページネーション）
    public function index(Request $request)
    {
        // keyword, genre, sort を使った検索処理を書く
        $books = Book::with(['genres'])
        ->withAvg('reviews', 'rating')
        ->orderBy('published_date', 'desc')
        ->paginate(10);

    return view('books.index', compact('books'));
    }

    // 書籍詳細
    public function show(Book $book)
    {
        // Blade が使うリレーションをすべてロード
        $book->load([
            'reviews.user',
            'reviews.likedByUsers',   // ★ Blade の likedByUsers に合わせる
            'genres',
            'favorites'               // ★ Book 側の favorites（User一覧）
        ]);

        // レビュー一覧（最新順）
        $reviews = $book->reviews->sortByDesc('created_at');

        // ジャンル一覧
        $genres = $book->genres;

        // お気に入り判定のために favoriteBooks をロード
        if (auth()->check()) {
            auth()->user()->load('favoriteBooks'); // ★ Blade の favoriteBooks に合わせる
            auth()->user()->load('likedReviews');
        }

        // Blade が使うお気に入り判定
        $isFavorite = auth()->check()
            ? auth()->user()->favoriteBooks->contains($book->id)
            : false;

        // Blade が使う「レビューいいね」判定
        $isLiked = auth()->check()
            ? ReviewLike::where('user_id', auth()->id())
                ->whereIn('review_id', $book->reviews->pluck('id'))
                ->exists()
            : false;

        return view('books.show', compact(
            'book',
            'reviews',
            'genres',
            'isFavorite',
            'isLiked'
        ));
    }


    // 書籍登録画面
    public function create()
    {
        $genres = Genre::all(); // Blade の $genres に合わせる

        return view('books.create', compact('genres'));
    }

    // 書籍登録
    public function store(StoreBookRequest $request)
    {
        // ★ ここで自動的にバリデーション済み
        $validated = $request->validated();

        // 書籍を作成
        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // ジャンルを紐づける（Blade の genres[] に合わせる）
        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を登録しました');
    }

    // 書籍編集画面
    public function edit(Book $book)
    {
        // 認可（作成者のみ）
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    // 書籍更新
    public function update(UpdateBookRequest $request, Book $book)
    {
        // 認可（作成者のみ）
        $this->authorize('update', $book);

        $validated = $request->validated();

        // 書籍情報を更新
        $book->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        // ジャンルを更新
        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を更新しました');
    }

    // 書籍削除
    public function destroy(Book $book)
    {
        // 認可（作成者本人のみ）
        $this->authorize('delete', $book);

        // 関連レビューのいいねを削除
        foreach ($book->reviews as $review) {
            $review->likedByUsers()->detach();
        }

        // 関連レビューを削除
        $book->reviews()->delete();

        // お気に入りを削除
        $book->favorites()->detach();

        // ジャンル紐付けを削除
        $book->genres()->detach();

        // 書籍本体を削除
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を削除しました。');
    }

    // ISBN検索（Google Books API）
    public function isbnLookup($isbn)
    {
        // API 呼び出し → JSON返却 or フォーム初期値
    }

    // ランキング（レビュー平均TOP10）
    public function ranking()
    {
        // 平均評価の高い順に10件（仮）
        $books = Book::withAvg('reviews', 'rating')
        ->orderBy('reviews_avg_rating', 'desc')
        ->take(10)
        ->get();

    return view('books.ranking', compact('books'));
    }
}
