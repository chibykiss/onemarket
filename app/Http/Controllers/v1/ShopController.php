<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ShopRequest;
use App\Http\Resources\v1\ShopResource;
use App\Models\Owner;
use App\Models\Shop;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    use HttpResponses, ImageUpload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::all();
        return ShopResource::collection($shop);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShopRequest $request)
    {
        $request->validated($request->all());

        //make sure the owner has been approved
        $owner = Owner::where('id',$request->owner)->first();
        if ($owner->approved == '0') {
            return $this->error(message: 'you cannot add an unapproved owner');
        }

        if ($request->hasFile('tenancy_receipt')) {
            $treceipt = $this->UserImageUpload($request->file('tenancy_receipt'), 'tenancy_reciepts');
        } else {
            $treceipt = null;
        }
        $shop = Shop::create([
            "shop_number" => $request->shop_no,
            "plaza_name" => $request->plaza_name,
            "shop_address" => $request->shop_address,
            "tenancy_receipt" => $treceipt,
            "owner_id" => $request->owner,
            "gotten_via" => $request->via,
            "guarantor" => $request->guarantor,
            "known_for" => $request->known_for,
            "company_name" => $request->company_name,
            "guranteed" => 1,
            "approved" =>  "0",
        ]);
        $shop = new ShopResource($shop);
        return $this->success($shop);
    }


    //add existing owner to an existing shop
    public function shopowner(Request $request)
    {

        $request->validate([
            "owner_id" => "required|numeric|exists:owners,id",
            "shop_id" => "required|numeric|exists:shops,id",
            "via" => "required|string",
            "tenancy_receipt" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "guarantor" => "required|numeric|exists:owners,id",
            "known_for" => "required|numeric",
            "company_name" => "required|string",
        ]);

        //make sure the shop and the owner has been approved;
        $shop = Shop::find($request->shop_id);
        if($shop->approved == '0') return $this->error(message: 'shop has to be approved first');

        //check for owner approval
        $owner = Owner::find($request->owner_id);
        if($owner->approved == '0') return $this->error(message: "owner has to be approved first");

        //make sure the shop hasnt been assigned to an owner
        if($shop->owner_id !== null ) return $this->error(message: 'shop already assigned to an owner');

        if ($request->hasFile('tenancy_receipt')) {
            $treceipt = $this->UserImageUpload($request->file('tenancy_receipt'), 'tenancy_reciepts');
        } else {
            $treceipt = null;
        }
        $shop->update([
            'owner_id' => $request->owner_id,
            'gotten_via' => $request->via,
            'tenancy_receipt' => $treceipt,
            'guarantor' => $request->guarantor,
            'known_for' => $request->known_for,
            'company_name' => $request->company_name,
            'guranteed' => 1,
        ]);
        $newShop =  new ShopResource($shop);
        return $this->success($newShop);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($shop)
    {
        $shop = Shop::where([
            ['id', '=', $shop],
            ['approved', '=', '1']
        ])->first();
        return new ShopResource($shop);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shop)
    {
        $request->validate([
                "shop_no" => "required|string",
                "plaza_name" => "required|string",
                "shop_address" => "required|string",
                "tenancy_receipt" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
                "owner" => "numeric|exists:owners,id",
                "via" => "string",
                "guarantor" => "numeric|exists:owners,id",
                "known_for" => "numeric",
                "company_name" => "string",
        ]);

        // $shop = Shop::where('id', '=', $shop)->first();
        //make sure the shop and the owner has been approved;
       
        // if ($shop->approved == '1') return $this->error(message: 'shop has to be unapproved');

        //check for owner approval
        $owner = Owner::find($request->owner);
        if ($owner->approved == '0') return $this->error(message: "owner has to be approved first");


        if ($request->hasFile('tenancy_receipt')) {
            $treceipt = $this->UserImageUpload($request->file('tenancy_receipt'), 'tenancy_reciepts');
        } else {
            $treceipt = null;
        }

        $shop = Shop::find($shop);
        $shop->update([
            "shop_number" => isset($request->shop_no) ? $request->shop_no : $shop->shop_number,
            "plaza_name" => isset($request->plaza_name) ? $request->plaza_name : $shop->plaza_name,
            "shop_address" =>isset($request->shop_address) ? $request->shop_address : $shop->shop_address,
            "tenancy_receipt" => $treceipt,
            "owner_id" => isset($request->owner) ? $request->owner : $shop->owner,
            "gotten_via" => isset($request->via) ? $request->via : $shop->gotten_via,
            "guarantor" => isset($request->guarantor) ? $request->guarantor : $shop->guarantor,
            "known_for" => isset($request->known_for) ? $request->known_for : $shop->known_for,
            "company_name" => isset($request->company_name) ? $request->company_name : $shop->company_name,
            "guranteed" => 1,
        ]);

        $newShop = new ShopResource($shop);
        return $this->success($newShop);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shop)
    {
        $shop = Shop::where('id', '=', $shop)->first();

        //check if shop has been approved
        if($shop->approved === "1") return $this->error(message: 'approved shops cannot be deleted');
        
        //check if shop has an existing owner
        if($shop->owner_id !== null) return $this->error(message: 'shop cant be deleted because it has a owner');
        
        //Delete Shop
        $shop->delete();
        return $this->success(message: 'shop deleted permanently');
        
    }
}
