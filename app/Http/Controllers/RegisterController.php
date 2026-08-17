<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class RegisterController extends Controller
{
    /**
     * ユーザー登録画面を表示する
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.register'); 
    }
}
