<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * お気に入り書籍（多対多）
     *
     * @return BelongsToMany<Book>
     */
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites');
    }

    /**
     * いいねしたレビュー（多対多）
     *
     * @return BelongsToMany<Review>
     */
    public function reviewLikes(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes');
    }

    /**
     * いいねしたレビュー（エイリアス）
     *
     * @return BelongsToMany<Review>
     */
    public function likedReviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes');
    }

    /**
     * お気に入り書籍（タイムスタンプ付き）
     *
     * @return BelongsToMany<Book>
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites')
                    ->withTimestamps();
    }
}
