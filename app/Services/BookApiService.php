<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BookApiService
{
    public function fetchBooks()
    {
        $response = Http::get('https://example.com/api/books');

        return $response->json();
    }
}
