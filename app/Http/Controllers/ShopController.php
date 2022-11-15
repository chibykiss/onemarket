<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::where('approved','=', '1')->get();
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

        $shop = Shop::create([
            "shop_number" => $request->shop_no,
            "plaza_name" => $request->plaza_name,
            "shop_address" => $request->shop_address,
            "owner_id" => $request->owner,
            "gotten_via" => $request->via,
            "guarantor" => $request->guarantor,
            "known_for" => $request->known_for,
            "company_name" => $request->company_name,
            "guranteed" => $request->guaranteed,
            "approved" =>  "0",
        ]);
        return new ShopResource($shop);
    }


    //add existing owner to an existing shop
    public function shopowner(Request $request)
    {
        $request->validate([
            "owner_id" => "required|numeric|exists:owners,id",
            "shop_id" => "required|numeric|exists:shops,id",
            "via" => "required|string",
            "guarantor" => "required|numeric|exists:owners,id",
            "known_for" => "required|numeric",
            "company_name" => "required|string",
            "guaranteed" => "boolean",
        ]);

        $shop = Shop::find($request->shop_id);
        if($shop->owner_id !== null ) return $this->error(['message' => 'shop already assigned to an owner']);
        $shop->update([
            'owner_id' => $request->owner_id,
            'gotten_via' => $request->via,
            'guarantor' => $request->guarantor,
            'known_for' => $request->known_for,
            'company_name' => $request->company_name,
            'guranteed' => $request->guaranteed,
        ]);
        return new ShopResource($shop);
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
                "owner" => "numeric|exists:owners,id",
                "via" => "string",
                "guarantor" => "numeric|exists:owners,id",
                "known_for" => "numeric",
                "company_name" => "string",
                "guaranteed" => "boolean",
        ]);

        $shop = Shop::where('id', '=', $shop)->first();
        $shop->update([
            "shop_number" => isset($request->shop_no) ? $request->shop_no : $shop->shop_number,
            "plaza_name" => isset($request->plaza_name) ? $request->plaza_name : $shop->plaza_name,
            "shop_address" =>isset($request->shop_address) ? $request->shop_address : $shop->shop_address,
            "owner_id" => isset($request->owner) ? $request->owner : $shop->owner,
            "gotten_via" => isset($request->via) ? $request->via : $shop->gotten_via,
            "guarantor" => isset($request->guarantor) ? $request->guarantor : $shop->guarantor,
            "known_for" => isset($request->known_for) ? $request->known_for : $shop->known_for,
            "company_name" => isset($request->company_name) ? $request->company_name : $shop->company_name,
            "guranteed" => isset($request->guaranteed) ? $request->guaranteed : $shop->guranteed,
        ]);

        return new ShopResource($shop);
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
        if($shop->approved === "1"){
            $shop->delete();
            return $this->success([
                "status" => 'deleted with soft delete'
            ]);
        } else{
            $shop->forceDelete();
            return $this->success([
                "status" => 'deleted permanently'
            ]);
        }
    }
}
