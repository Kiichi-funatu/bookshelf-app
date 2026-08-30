<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ReviewLike;
use App\Models\Genre;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\SearchBooksRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;

class BookController extends Controller
{
    // 書籍一覧（検索・絞り込み・ソート・ページネーション）
    /*public function index(Request $request)
    {
        // keyword, genre, sort を使った検索処理を書く
        $books = Book::with(['genres'])
        ->withAvg('reviews', 'rating')
        ->orderBy('published_date', 'desc')
        ->paginate(10);

    return view('books.index', compact('books'));
    }*/
    
    /**
     * 書籍一覧（検索・絞り込み・ソート・ページネーション）
     *
     * @param SearchBooksRequest $request
     * @return View
     */
    public function index(SearchBooksRequest $request): View
    {
        $validated = $request->validated();

        $query = Book::query()
            ->with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // キーワード検索
        if (!empty($validated['keyword'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('title', 'like', '%' . $validated['keyword'] . '%')
                ->orWhere('author', 'like', '%' . $validated['keyword'] . '%');
            });
        }

        // ジャンル絞り込み（Bladeは genre）
        if (!empty($validated['genre'])) {
            $query->whereHas('genres', function ($q) use ($validated) {
                $q->where('genres.id', $validated['genre']);
            });
        }

        // 並び順ソート（Blade準拠）
        if (!empty($validated['sort'])) {
            switch ($validated['sort']) {
                case 'newest':
                    $query->orderBy('published_date', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('published_date', 'asc');
                    break;
                case 'rating':
                    $query->orderBy('reviews_avg_rating', 'desc');
                    break;
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
            }
        }

        // ★ sort が指定されていない場合は created_at の降順（最新順）
        if (empty($validated['sort'])) {
            $query->orderBy('created_at', 'desc');
        }

        // ページネーション（検索条件を引き継ぐ）
        $books = $query->paginate(10)->appends($validated);

        // ジャンル一覧（プルダウン用）
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    /**
     * 書籍詳細
     *
     * @param Book $book
     * @return View
     */
    public function show(Book $book): View
    {
        // Blade が使うリレーションをすべてロード
        $book->loadMissing([
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
            auth()->user()->load(['favoriteBooks', 'likedReviews']);
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


    /**
     * 書籍登録画面
     *
     * @return View
     */
    public function create(): View
    {
        $genres = Genre::all(); // Blade の $genres に合わせる

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍登録
     *
     * @param StoreBookRequest $request
     * @return RedirectResponse
     */
    public function store(StoreBookRequest $request): RedirectResponse
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

    /**
     * 書籍編集画面
     *
     * @param Book $book
     * @return View
     */
    public function edit(Book $book): View
    {
        // 認可（作成者のみ）
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍更新
     *
     * @param UpdateBookRequest $request
     * @param Book $book
     * @return RedirectResponse
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
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

    /**
     * 書籍削除
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function destroy(Book $book): RedirectResponse
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

    /**
     * ISBN検索（Google Books API）
     *
     * @param string $isbn
     * @return \Illuminate\Http\JsonResponse
     */
    public function isbnLookup($isbn)
    {
        // Open Library API 呼び出し
        $response = Http::get("https://openlibrary.org/isbn/{$isbn}.json");

        if ($response->failed()) {
            return response()->json(['error' => '書籍情報の取得に失敗しました'], 500);
        }

        $data = $response->json();

        // 著者名の取得（Open Library は author の参照が別API）
        $authorName = null;
        if (!empty($data['authors'][0]['key'])) {
            $authorResponse = Http::get("https://openlibrary.org{$data['authors'][0]['key']}.json");
            if ($authorResponse->ok()) {
                $authorJson = $authorResponse->json();
                $authorName = $authorJson['name'] ?? null;
            }
        }

        // 説明（description）は文字列 or 配列の両方がある
        $description = null;
        if (!empty($data['description'])) {
            $description = is_array($data['description'])
                ? ($data['description']['value'] ?? null)
                : $data['description'];
        }

        // カバー画像
        $imageUrl = null;
        if (!empty($data['covers'][0])) {
            $imageUrl = "https://covers.openlibrary.org/b/id/{$data['covers'][0]}-L.jpg";
        }

        return response()->json([
            'title'          => $data['title'] ?? null,
            'author'         => $authorName,
            'published_date' => $data['publish_date'] ?? null,
            'description'    => $description,
            'image_url'      => $imageUrl,
        ]);
    }
}
