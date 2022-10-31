<?php

namespace App\Http\Controllers\Api\Ext\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Ext\Group\GroupCollectionResource;
use App\Http\Resources\Api\Ext\Group\GroupSingleResource;
use App\Models\Group\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::all();

        $groups->load('groupColumns');

        return response()->json(GroupCollectionResource::collection($groups));
    }

    public function show(Request $request, Group $group)
    {
        return response()->json(new GroupSingleResource($group));
    }

}

