<?php

namespace App\Http\Controllers\Api\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Group\StoreGroupRequest;
use App\Http\Requests\Api\Group\UpdateGroupRequest;
use App\Http\Resources\Api\Group\GroupCollectionResource;
use App\Http\Resources\Api\Group\GroupSingleResource;
use App\Jobs\Category\CacheCategoryJob;
use App\Models\Group\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(GroupCollectionResource::collection(Group::all()));
    }

    public function store(StoreGroupRequest $request)
    {
        $group = Group::create($request->all());

        CacheCategoryJob::dispatch();

        return response()->json(new GroupSingleResource($group->fresh()));
    }

    public function show(Request $request, ... $groupName)
    {
        $group = Group::where('id', $groupName)
            ->orWhere('name', $groupName)
            ->orWhere('de', $groupName)
            ->orWhere('en', $groupName)
            ->orWhere('fr', $groupName)
            ->orWhere('it', $groupName)
            ->first();

        return response()->json(new GroupSingleResource($group->fresh()));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->only('name', 'de', 'en', 'fr', 'it', 'active'));

        CacheCategoryJob::dispatch();

        return response()->json(new GroupSingleResource($group->fresh()));
    }

    public function destroy(Request $request, Group $group)
    {
        if($group->importRules->count() === 0) {
            $group->delete();

            CacheCategoryJob::dispatch();

            return response('success', 200);
        }
        else {
            return response(["message" => "Group can't be deleted!"], 422);
        }
    }
}

