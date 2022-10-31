<?php

namespace App\Http\Controllers\Api\CustomsSetting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomsSetting\StoreCustomsSettingRequest;
use App\Models\CustomsSetting\CustomsSetting;

class CustomsSettingController extends Controller
{
    public function index()
    {
        return response()->json(CustomsSetting::all());
    }

    public function store(StoreCustomsSettingRequest $request)
    {
        $customsSetting = CustomsSetting::create($request->all());

        return response()->json($customsSetting);
    }
}
