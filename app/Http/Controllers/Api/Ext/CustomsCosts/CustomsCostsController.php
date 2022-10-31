<?php

namespace App\Http\Controllers\Api\Ext\CustomsCosts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomsCostsController extends Controller
{
    public function index (Request $request)
    {
        return [
            "value"     =>  0,
            "currency"  =>  "EUR"
        ];
    }
}
