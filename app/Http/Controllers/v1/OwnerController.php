<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OwnerRequest;
use App\Http\Resources\v1\OwnerResource;
use App\Models\Owner;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserCategoryJoin;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    use ImageUpload, HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $owners = Owner::where('approved','=', "1")->get();
        $owners = $owners->load('user');
        return OwnerResource::collection($owners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OwnerRequest $request)
    {
        $request->validated($request->all());

        //check if the userid has been approved before making owner
        $user = User::find($request->user_id);
        if($user->approved !== "1") return $this->error(['message' => 'user is not approved']);

        //get shop
        $shop = Shop::find($request->shop_id);

        //make sure shop has been approved first
        if($shop->approved !== "1") return $this->error(['message' => 'shop has not been approved']);

        //make sure shop has not been assigned to another owner
        if ($shop->owner_id !== null) return $this->error(['message' => 'shop already assigned to an owner']);
        
        //deal with the images uploaded
        $rpath = $this->UserImageUpload($request->file('tenancy_receipt'), 'tenancy_reciepts');
        $regpath = $this->UserImageUpload($request->file('reg_receipt'), 'registration_reciepts');
        if ($request->hasFile('cert')) {
            $cert = $this->UserImageUpload($request->file('cert'), 'owner_certificates');
        }else{
            $cert = null;
        }
        // $owner = DB::transaction(function() use ($request,$rpath,$regpath,$cert) {
            DB::beginTransaction();
            //Owner::where()
            $owner = Owner::create([
                'user_id' => $request->user_id,
                'owner_served' => $request->owner_served,
                'previous_job' => $request->coming_from,
                'reg_receipt' => $regpath,
                'cert' => $cert,
                'approved' => "0",
            ]);

            
            //update shop details
            $updateshop = $shop->update([
                'owner_id' => $owner->id,
                'tenancy_receipt' => $rpath,
                'gotten_via' => $request->via,
                'guarantor' => $request->guarantor,
                'known_for' => $request->known_for,
                'company_name' => $request->company_name,
                'guranteed' => 1,
            ]);

            if(!$updateshop){
                DB::rollBack();
            }

            //give the user a category of an owner
            $put = UserCategoryJoin::create([
                'user_id' => $request->user_id,
                'UserCategory_id' => 2,
            ]);
            if(!$put){
                DB::rollBack();
            }
            DB::commit();
            return new OwnerResource($owner);

        // });
        //return $owner;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Owner $owner)
    {
        return $this->success([
            "owner" => $owner,
            "shops" => $owner->shops,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOwner(Request $request,Owner $owner)
    {
        // if($owner->approved == 1){
        //     return $this->error(['message' => 'you cant update an approved owner']);
        // }
        $request->validate([
              'user_id' => 'numeric||exists:users,id|unique:owners,user_id,'.$owner->id,
              'shop_id' => 'numeric|exists:shops,id',
              'owner_served' => 'numeric|exists:owners,id',
              'coming_from' => 'string',
              'tenancy_receipt' => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
              'reg_receipt' => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
              'cert' => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
              'via' => 'required|string',
              'guarantor' => "required|numeric|exists:owners,id",  
              'known_for' => 'required|numeric',
              'company_name' => 'required|string',
              'guaranteed' => 'boolean',
              'active' => 'boolean',
        ]);
        // //get shop
        $shop = Shop::find($request->shop_id);
        if ($shop->owner_id !== $owner->id && $shop->owner_id !== null) {
            return $this->error(['message' => 'shop already assigned to an owner']);
        }
        
        $cert = $request->hasFile('cert') 
        ? $this->UserImageUpload($request->file('cert'), 'owner_certificates', $owner->cert)
        : $owner->cert;

        $regpath = $request->hasFile('reg_receipt')
        ? $this->UserImageUpload($request->file('reg_receipt'), 'registration_reciepts', $owner->reg_receipt)
        : $owner->reg_receipt;

        $rpath = $request->hasFile('tenancy_receipt')
        ? $this->UserImageUpload($request->file('tenancy_receipt'), 'tenancy_reciepts', $owner->tenancy_receipt)
        : $owner->tenancy_receipt;

        DB::beginTransaction();
        $owner->update([
            'user_id' => isset($request->user_id) ? $request->user_id : $owner->user_id,
            'owner_served' => isset($request->owner_served) ? $request->owner_served : null,
            'previous_job' => isset($request->coming_from) ? $request->coming_from : null,
            'tenancy_receipt' => $rpath,
            'reg_receipt' => $regpath,
            'cert' => $cert,
            'approved' => $owner->approved,
        ]);
        //return response()->json($owner);

        
        if($shop->owner_id === null){
            $formerShop = Shop::where('owner_id','=',$owner->id);
            $formerShop->update([
                'owner_id' => null,
                'gotten_via' => null,
                'guarantor' => null,
                'known_for' => null,
                'company_name' => null,
                'guranteed' => null,
            ]);    
        }

        $updateit = $shop->update([
            'owner_id' => $owner->id,
            'gotten_via' => $request->via,
            'guarantor' => $request->guarantor,
            'known_for' => $request->known_for,
            'company_name' => $request->company_name,
            'guranteed' => $request->guranteed,
        ]);
        if(!$updateit){
            DB::rollBack();
            return $this->error(["message" => 'an error occured with the update']);
        }

        DB::commit();
        return response()->json($shop);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Owner $owner)
    {
        if($owner->approved !== "1"){
            DB::beginTransaction();
            $shop = Shop::where('owner_id', '=', $owner->id);
            $updateshop = $shop->update([
                'owner_id' => null,
                'gotten_via' => null,
                'guarantor' => null,
                'known_for' => null,
                'company_name' => null,
                'guranteed' => null,
            ]);
            if(!$updateshop){ 
                DB::rollBack();
                return $this->error(['message' => 'error with shop deletion']);
             }
            
               $deljoin = UserCategoryJoin::where('user_id',$owner->user_id)->delete();
               if(!$deljoin){
                DB::rollBack();
               }
                $owner->delete();
               DB::commit();
               return $this->success([
                   "status" => 'Owner deleted permanently'
               ]);
        }else{
            return $this->success([
                "message" => "Owner has been approved therefore cannot be deleted"
            ]);
        }
    }

}
