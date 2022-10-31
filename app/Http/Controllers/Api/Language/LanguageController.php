<?php

namespace App\Http\Controllers\Api\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(config('language'));
    }

}

