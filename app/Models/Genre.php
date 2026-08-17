<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * 書籍（多対多）
     *
     * @return BelongsToMany<Book>
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_genre');
    }

    /**
     * 書籍数（アクセサ）
     *
     * @return int
     */
    public function getBookCountAttribute(): int
    {
        return $this->books()->count();
    }
}
