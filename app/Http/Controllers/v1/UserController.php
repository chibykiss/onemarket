<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Traits\Helpers;

use App\Http\Requests\v1\storeUserRequest;
use App\Http\Requests\v1\updateUserRequest;
use App\Http\Resources\v1\UserResource;
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
        $users = User::with('categories','owner.shops','attachee.shop','worker.shop','apprentice.shop','taskforce')->get();
        return $this->success($users);
        //return UserResource::collection(User::all());
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
    public function show(User $member)
    {
        if($member->exists()){
            $new_member = new UserResource($member);
            return $this->success($new_member);
        }else{
            return $this->error(message:'user does not exist',code:404);
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
    public function update(updateUserRequest $request, User $member)
    {
        $request->validated($request->all());
        if ($member->approved == 1) {
            return $this->error(message: 'you cannot update an approved member details');
        }
            if($request->hasFile('profile_pic')){
                $currentImgName = $member->profile_pic;
                $image = $request->file('profile_pic');
                $filePath = $this->UserImageUpload($image, 'profile_images',$currentImgName);
            }else{
                $filePath = $member->profile_pic;
            }
        
            $member->update([
                'firstname' => isset($request->firstname) ? $request->firstname : $member->firstname,
                'lastname' => isset($request->lastname) ? $request->lastname : $member->lastname,
                'middlename' => isset($request->middlename) ? $request->middlename : $member->middlename,
                'username' => isset($request->username) ? $request->username : $member->username,
                'profile_pic' => $filePath,
                'email' => isset($request->email) ? $request->email : $member->email,
                'phone_number' => isset($request->phone_number) ? $request->phone_number : $member->phone_number,
                'nationality' => isset($request->nationality) ? $request->nationality : $member->nationality,
                'sex' => isset($request->sex) ? $request->sex : $member->sex,
                'marital_status' => isset($request->marital_status) ? $request->marital_status : $member->marital_status,
                'date_of_birth' => isset($request->dob) ? $request->dob : $member->date_of_birth,
                'password' => isset($request->password)
                    ? Hash::make(strtolower($request->password))
                    : $member->password
            ]);
            $newUser = new UserResource($member);
            return $this->success($newUser);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $member)
    {
            $approved = $this->isApproved($member);
            if($approved == 'approved'){
                return $this->error(message:'user cannot be deleted because it has been approved');
            }
            $member->delete();
            return $this->success(message:'deleted sucessfully');
   
    }
}
