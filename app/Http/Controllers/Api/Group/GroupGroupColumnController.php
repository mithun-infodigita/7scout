<?php

namespace App\Http\Controllers\Api\Group;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\GroupColumn\StoreGroupColumnRequest;
use App\Http\Requests\Api\GroupColumn\UpdateGroupColumnRequest;
use App\Http\Resources\Api\Group\GroupColumn\GroupColumnCollectionResource;
use App\Http\Resources\Api\Group\GroupColumn\GroupColumnSingleResource;
use App\Jobs\Category\CacheCategoryJob;
use App\Models\Group\Group;
use App\Models\GroupColumn\GroupColumn;
use Illuminate\Http\Request;

class GroupGroupColumnController extends Controller
{
    public function index(Request $request, Group $group)
    {
        return response()->json(GroupColumnCollectionResource::collection($group->groupColumns->sortBy('order_column')));
    }

    public function store(StoreGroupColumnRequest $request, Group $group)
    {
        $group->groupColumns()->create($request->all());

        CacheCategoryJob::dispatch();
    }

    public function show(Request $request, Group $group, GroupColumn $groupColumn)
    {

        return response()->json(new GroupColumnSingleResource($groupColumn));
    }

    public function update(UpdateGroupColumnRequest $request, Group $group, GroupColumn $groupColumn)
    {
        $groupColumn->update($request->all());

        CacheCategoryJob::dispatch();

        return response()->json(new GroupColumnSingleResource($groupColumn->fresh()));
    }

    public function destroy(Request $request, Group $group, GroupColumn $groupColumn)
    {
        $groupColumn->delete();

        CacheCategoryJob::dispatch();

        return response('success', 200);
    }

    public function updateOrder(Request $request)
    {
        GroupColumn::setNewOrder($request->orderIds);

        CacheCategoryJob::dispatch();
    }
}

