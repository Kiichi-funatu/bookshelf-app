<?php

namespace App\Http\Controllers;

use App\Services\BookApiService;

class ApiBookController extends Controller
{
    public function index(BookApiService $service)
    {
        return response()->json($service->fetchBooks());
    }
}
