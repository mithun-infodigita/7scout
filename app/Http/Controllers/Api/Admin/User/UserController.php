<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\User\StoreUserRequest;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index (Request $request) {
        return response()->json(User::all());
    }

    public function store(StoreUserRequest $request)
    {
        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        $user = User::create($request->all());

        return response()->json($user);
    }

    public function show(Request $request, User $user)
    {
        return response()->json($user);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        $user->update([
            'password'  =>  $request->password
        ]);

        return response()->json($user);
    }
}

