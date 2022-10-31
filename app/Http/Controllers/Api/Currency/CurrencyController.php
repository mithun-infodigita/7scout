<?php

namespace App\Http\Controllers\Api\Currency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(config('currency'));
    }

}

