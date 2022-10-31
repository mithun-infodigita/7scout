<?php

namespace App\Http\Controllers\Api\Country;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(config('country'));
    }

}

