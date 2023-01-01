<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\AttacheeResource;
use App\Models\Attachee;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserCategoryJoin;
use App\Traits\Helpers;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AttacheeController extends Controller
{
    use ImageUpload, HttpResponses, Helpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $attachees = Attachee::all();
        // $attachees = $attachees->load('user'); // using the load method for relationships
        $attachees = Attachee::with('user')->get(); //method two
        return AttacheeResource::collection($attachees);
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
            'user_id' => 'required|numeric|unique:attachees,user_id|exists:users,id',
            'shop_id' => 'required|numeric|unique:attachees,shop_id|exists:shops,id',
            'cover_letter' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        //make sure user has been approved before making him 
         $user = User::find($request->user_id);
        if($user->approved !== "1") return $this->error(['message' => 'user is not approved']);

        //make sure the shop has been assigned to an owner
        $shop = Shop::find($request->shop_id);
        if($shop->owner_id === null) return $this->error(['message' => 'shop does not have an owner']);

        $letter = $this->UserImageUpload($request->file('cover_letter'), 'attachee_request_letters');

        DB::beginTransaction();
        $attachee = Attachee::create([
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'attachee_letter' => $letter,
        ]);

        //give the user a category of an Attachee
        $put = UserCategoryJoin::create([
            'user_id' => $request->user_id,
            'UserCategory_id' => 3,
        ]);
        if (!$put) {
            DB::rollBack();
        }
        DB::commit();
        return new AttacheeResource($attachee);
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
    public function destroy(Attachee $attachee)
    {
        //if attachee has been approved he cant be removed
        if($attachee->approved === 1) return $this->error('','attachee has to be unapproved to be removed',422);

        DB::beginTransaction();
        $deljoin = UserCategoryJoin::where('user_id', $attachee->user_id)->delete();
        if (!$deljoin) {
            DB::rollBack();
        }
        $attachee->delete();
        DB::commit();
        /*   DELETE COVER LETTER ASSOCIATED WITH ATTACHEE  */
        $imgpath = storage_path("app/public/attachee_request_letters/" . $attachee->attachee_letter);
        if (File::exists($imgpath)) {
            File::delete($imgpath);
        }
        return $this->success(['message' => 'removed'],'the attachee has been removed');
    }
}
