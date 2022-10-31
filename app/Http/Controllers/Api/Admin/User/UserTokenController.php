<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\User\Token\StoreTokenRequest;
use App\Http\Resources\Api\Admin\Token\TokenCollectionResource;
use App\Models\User\User;
use Illuminate\Http\Request;

class UserTokenController extends Controller
{
    public function index(Request $request, User $user)
    {
        return response()->json(TokenCollectionResource::collection($user->tokens));
    }

    public function store(StoreTokenRequest $request, User $user)
    {
        $token = $user->createToken($request->name);

        return response()->json($token);
    }

    public function destroy(Request $request, User $user, ... $token)
    {
        $user->tokens()->where('id', $token)->first()->delete();

        return response('success', 200);
    }
}

