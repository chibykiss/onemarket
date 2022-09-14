<?php

namespace App\Http\Controllers;

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
    use HttpResponses,ImageUpload;
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
    public function show($user)
    {
        $member = User::where('id',$user);
        //$check_user = $user->exist();
        if($member->exists()){
            return new UserResource($member->first());
        }else{
            return $this->error('','user does not exist',404);
        }
        
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
        //$real_user = User::find($user);
        if($member->exists()){
            $real_user = $member->first();
            if($request->hasFile('profile_pic')){
                $currentImgName = $real_user->profile_pic;
                $image = $request->file('profile_pic');
                $filePath = $this->UserImageUpload($image, 'profile_images',$currentImgName);
            }else{
                $filePath = $real_user->profile_pic;
            }
            if(isset($request->phone_number)){
                $trim_num = ltrim($request->phone_number, '0');
                $trim_num = '234'. $trim_num;
            }else{
                $trim_num = $real_user->phone_number;
            }
        
            $real_user->update([
                'fullname' => isset($request->fullname)? $request->fullname : $real_user->fullname,
                'username' => isset($request->username) ? $request->username:$real_user->username,
                'profile_pic' => $filePath,
                'email' => isset($request->email) ? $request->email:$real_user->email,
                'phone_number' => $trim_num,
                'user_categories_id' => isset($request->user_type) ? $request->user_type:$real_user->user_categories_id,
                'password' => isset($request->password)
                                ?Hash::make(strtolower($request->password))
                                :$real_user->password
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
    public function destroy($user)
    {
        $member = User::where('id',$user);
        if($member->exists()){
            $member->delete();
            return $this->success('deleted sucessfully');
        }else{
            return $this->error('resource not found', code:404);
        }
     
    }
}
