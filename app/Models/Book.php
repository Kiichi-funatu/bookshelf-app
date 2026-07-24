<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_date',
        'description',
        'image_url',
        'user_id', // 作成者
    ];

    protected $casts = [
        'published_date' => 'date',
    ];

    // 作成者（User）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ジャンル（多対多）
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre');
    }

    // レビュー（1対多）
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // お気に入り（多対多）
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // 平均評価（アクセサ）
    public function getAvgRatingAttribute()
    {
        return $this->reviews()->avg('rating');
    }

    // レビュー件数
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
}

