<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TranslationsController extends Controller
{
    public function index()
    {
        return view('admin.translations.index');
    }
}
