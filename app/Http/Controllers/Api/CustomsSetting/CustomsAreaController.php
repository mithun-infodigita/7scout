<?php

namespace App\Http\Controllers\Api\CustomsSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomsAreaController extends Controller
{
    public function index()
    {
        return response()->json(config('customsSetting.customsAreas'));
    }
}
