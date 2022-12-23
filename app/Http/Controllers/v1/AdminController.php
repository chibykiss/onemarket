<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\AdminResource;
use App\Models\Admin;
use App\Models\UserCategoryJoin;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $admins = Admin::with('user')->get();
            return AdminResource::collection($admins);
        
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
                'user_id' => 'required|numeric|unique:admins,user_id',
                'admin_type' => 'required|String',
                'user_type' => 'required|numeric',
            ]);
            
             $admin = DB::transaction(function () use ($request) {
                     //create the admin
                $admin = Admin::create([
                    'user_id' => $request->user_id,
                    'admin_type' => $request->admin_type,
                    'user_categories_id' => $request->user_type,
                ]);
               
                //update the join user and category table
                UserCategoryJoin::create([
                    'user_id' => $request->user_id,
                    'UserCategory_id' => $request->user_type
                ]);
                // $user->update([
                //     'user_categories_id' => $request->user_type
                // ]);
                 return $admin;
            });
            return new AdminResource($admin);
        
 

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return new AdminResource($admin);
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
    public function destroy(Admin $admin)
    {
        try{
            DB::transaction(function() use($admin) {
                //$user = User::find($admin->user_id);
                UserCategoryJoin::where([
                    ['user_id', '=', $admin->user_id],
                    ['UserCategory_id', '=', 1]
                ])->delete();
                 
                $admin->delete();
            });
            return $this->success([
                "message" => "admin has been removed successfully"
            ]);
        }catch(\Exception $e){
            return $this->error([
                "message" => $e->getMessage()
            ], 'admin could not be deleted');
        }
      
    }
}
