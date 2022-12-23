<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ApprenticeResource;
use App\Models\Apprentice;
use App\Models\User;
use App\Models\UserCategoryJoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprenticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        ]);

        //make sure user has been approved before making him 
        $user = User::find($request->user_id);
        if ($user->approved !== "1") return $this->error(['message' => 'user is not approved']);


        DB::beginTransaction();
        $apprentice = Apprentice::create([
            'user_id' => $request->user_id,
        ]);

        //give the user a category of an Apprentice
        $put = UserCategoryJoin::create([
            'user_id' => $request->user_id,
            'UserCategory_id' => 4,
        ]);
        if (!$put) {
            DB::rollBack();
        }
        DB::commit();
        return new ApprenticeResource($apprentice);
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
    public function destroy($id)
    {
        //
    }
}
