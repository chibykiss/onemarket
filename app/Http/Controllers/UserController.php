<?php

namespace App\Http\Controllers;

use App\Traits\Helpers;
use App\Http\Requests\storeUserRequest;
use App\Http\Requests\updateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses,ImageUpload, Helpers;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(storeUserRequest $request)
    {
        // $request->validated($request->all());
        // $request->validate([
        //     "fullname" => "String|required",
        //     "username" => "String|required",
        //     "email" => "email|unique:users,email",
        //     "phone_number" => "String|unique:users,phone_number",
        //     "userCategory_id" => "required",
        //     "password" => "required",
        //     'profile_pic' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);
        // $user = new User();
        // if ($request->hasFile('profile_pic')) {
        //     $image = $request->file('profile_pic');
        //     $image_name = Str::random(20);
        //     $ext = strtolower($image->getClientOriginalExtension()); // You can use also getClientOriginalName()
        //     $filePath = $image_name . '.' . $ext;
        //     $upload_path = "public/profile_images";    //Creating Sub directory in Public folder to put image
        //     $image->storeAs($upload_path, $filePath);
        // } else {
        //     $filePath = "noimage.jpg";
        // }
        // $user->profile_pic = $filePath;
        // $user->fullname = $request->fullname;
        // $user->username = $request->username;
        // $user->email = $request->email;
        // $user->phone_number = $request->phone_number;
        // $user->user_categories_id = $request->userCategory_id;
        // $user->password = Hash::make(strtolower($request->password));
        // $user->save();
        // return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // $member = User::where([
        //     ['id', '=',$user],
        //     ['approved', '=', '1'],
        // ]);
        //$check_user = $user->exist();
        if($user->exists()){
            return new UserResource($user);
        }else{
            return $this->error('','user does not exist',404);
        }
        
    }

    public function getUsercategory(){
        $user_cats = auth()->user()->categories;
        //return response()->json(count($user_cats));
        if(count($user_cats) === 0){
            return response()->json([
                "data" => "its empty"
            ]); 
        }else{
            return response()->json([
                "data" => "you are an admin"
            ]); 
        }
        //  foreach(auth()->user()->categories as $cat){
        //     $cats_id[] = $cat->id;
        //  }
        //  if(in_array(1,$cats_id)){
        //     return response()->json([
        //         "data" => "you are an admin"
        //     ]);
        //  }else{
        //     return response()->json([
        //         "data" => "you are not an admin"
        //     ]);
        //  }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(updateUserRequest $request, $user)
    {
        $request->validated($request->all());
        $member = User::where('id', $user);
        if($this->isApproved($member) == 'approved'){
            return $this->error('you cannot update an approved member details');
        }
        if($member->exists()){
            $real_user = $member->first();
            if($request->hasFile('profile_pic')){
                $currentImgName = $real_user->profile_pic;
                $image = $request->file('profile_pic');
                $filePath = $this->UserImageUpload($image, 'profile_images',$currentImgName);
            }else{
                $filePath = $real_user->profile_pic;
            }
        
            $real_user->update([
                'firstname' => isset($request->fullname) ? $request->fullname : $real_user->fullname,
                'lastname' => isset($request->lastname) ? $request->lastname : $real_user->lastname,
                'middlename' => isset($request->middlename) ? $request->middlename : $real_user->middlename,
                'username' => isset($request->username) ? $request->username : $real_user->username,
                'profile_pic' => $filePath,
                'email' => isset($request->email) ? $request->email : $real_user->email,
                'phone_number' => isset($request->phone_number) ? $request->phone_number : $real_user->phone_number,
                'nationality' => isset($request->nationality) ? $request->nationality : $real_user->nationality,
                'sex' => isset($request->sex) ? $request->sex : $real_user->sex,
                'marital_status' => isset($request->marital_status) ? $request->marital_status : $real_user->marital_status,
                'date_of_birth' => isset($request->dob) ? $request->dob : $real_user->date_of_birth,
                'password' => isset($request->password)
                    ? Hash::make(strtolower($request->password))
                    : $real_user->password
            ]);
            return new UserResource($real_user);
        }else{
            return $this->error([
                'message' => 'member not found'
            ],code: 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        
        //$member = User::where('id',$user);
        // if($user->approved == '1'){
        //     return $this->success('approved user cannot be deleted');
        // }
        if($user->exists()){
            $approved = $this->isApproved($user);
            if($approved == 'approved'){
                return $this->error('user cannot be deleted');
            }
            $user->delete();
            return $this->success('deleted sucessfully');
        }else{
            return $this->error('resource not found', code:404);
        }
     
    }
}
