<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 書籍（多対多）
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_genre');
    }

    // 書籍数
    public function getBookCountAttribute()
    {
        return $this->books()->count();
    }
}
