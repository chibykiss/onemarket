<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\WorkerResource;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserCategoryJoin;
use App\Models\Worker;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workers = Worker::with('user')->get();
        return WorkerResource::collection($workers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|unique:apprentices,user_id|exists:users,id',
            'shop_id' => 'required|numeric|unique:apprentices,shop_id|exists:shops,id',
        ]);

        //make sure user has been approved before making him 
        $user = User::find($request->user_id);
        if ($user->approved !== "1") return $this->error(['message' => 'user is not approved'],code:422);

        //make sure the shop has been assigned to an owner
        $shop = Shop::find($request->shop_id);
        if ($shop->owner_id === null) return $this->error(['message' => 'shop does not have an owner'],code:422);

        DB::beginTransaction();
        $worker = Worker::create([
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
        ]);

        //give the user a category of an Apprentice
        $put = UserCategoryJoin::create([
            'user_id' => $request->user_id,
            'UserCategory_id' => 5,
        ]);
        if (!$put) {
            DB::rollBack();
        }
        DB::commit();
        return new WorkerResource($worker);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Worker $worker)
    {
        //if worker has been approved he cant be removed
        if ($worker->approved === 1) return $this->error('', 'worker has to be unapproved to be removed', 422);

        DB::beginTransaction();
        $deljoin = UserCategoryJoin::where('user_id', $worker->user_id)->delete();
        if (!$deljoin) {
            DB::rollBack();
        }
        $worker->delete();
        DB::commit();

        return $this->success(['message' => 'removed'], 'the worker has been removed');
    }
}
