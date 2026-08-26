<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_date',
        'description',
        'image_url',
        'user_id', // 作成者
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_date' => 'date',
    ];

    /**
     * 作成者（User）
     *
     * @return BelongsTo<User, Book>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ジャンル（多対多）
     *
     * @return BelongsToMany<Genre>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'book_genre');
    }

    /**
     * レビュー（1対多）
     *
     * @return HasMany<Review>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * お気に入り（多対多）
     *
     * @return BelongsToMany<User>
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * 平均評価（アクセサ）
     *
     * @return float|null
     */
    public function getAvgRatingAttribute(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * レビュー件数（アクセサ）
     *
     * @return int
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * レビュー平均評価を返す
     *
     * @return float|null
     */
    public function averageRating(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * キーワード検索スコープ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKeyword($query, ?string $keyword)
    {
        if (!empty($keyword)) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }


}

