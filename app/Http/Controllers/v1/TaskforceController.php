<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\TaskforceResource;
use App\Models\Taskforce;
use App\Models\User;
use App\Models\UserCategoryJoin;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskforceController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskforces = Taskforce::with('user')->get();
        $alltaskforce = TaskforceResource::collection($taskforces);
        return $this->success($alltaskforce);

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
            'user_id' => 'required|numeric|unique:taskforces,user_id|exists:users,id',
            'taskforce_type' => 'required|string',
        ]);

        //make sure user has been approved before making him 
        $user = User::find($request->user_id);
        if ($user->approved !== "1") return $this->error(message: 'user is not approved');

    

        DB::beginTransaction();
        $taskforce = Taskforce::create([
            'user_id' => $request->user_id,
            'taskforce_type' => $request->taskforce_type,
        ]);

        //give the user a category of an Apprentice
        $put = UserCategoryJoin::create([
            'user_id' => $request->user_id,
            'UserCategory_id' => 6,
        ]);
        if (!$put) {
            DB::rollBack();
        }
        DB::commit();
        $newTaskforce = new TaskforceResource($taskforce);
        return $this->success($newTaskforce);
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
    public function destroy(Taskforce $taskforce)
    {
        //if worker has been approved he cant be removed
        if ($taskforce->approved === 1) return $this->error(message:'Taskforce has to be unapproved to be removed');

        DB::beginTransaction();
        $deljoin = UserCategoryJoin::where('user_id', $taskforce->user_id)->delete();
        if (!$deljoin) {
            DB::rollBack();
        }
        $taskforce->delete();
        DB::commit();

        return $this->success(message: 'the Taskforce has been removed');
    }
}
