<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;


use App\Http\Requests\v1\storeUserRequest;
use App\Http\Requests\v1\updateUserRequest;
use App\Http\Resources\v1\UserLoginResource;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses,ImageUpload;
    public function register(storeUserRequest $request)
    {
        $request->validated($request->all());
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $filePath = $this->UserImageUpload($image, 'profile_images');
        } else {
            $filePath = "noimage.jpg";
        }
        try{
                $user = User::create([
                    'profile_pic' => $filePath,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'lastname' => $request->lastname,
                    'username' => $request->username,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'nationality' => $request->nationality,
                    'sex' => $request->sex,
                    'marital_status' => $request->marital_status,
                    'date_of_birth' => $request->dob,
                    'user_categories_id' => 8,
                    'password' => Hash::make(strtolower($request->password)),
                ]);
                // UserCategoryJoin::create([
                //     'user_id' => $user->id,
                //     'UserCategory_id' => 8
                // ]);
            return new UserResource($user);
        }catch(\Exception $e){
            return $this->error([
                "message" => $e->getMessage()
            ],'user could not be created');
        }
        // return $this->success([
        //     "user" => $user_gotten,
        //     //"token" => $user->createToken('API Token of '.$user->username)->plainTextToken
        // ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|String',
            'password' => 'required|String',
        ]);

        if(!Auth::attempt($request->only(['username','password']))){
            return $this->error("","Credentials do not match",401);
        }

            //$user = User::where('username',$request->username)->first();
        $getuser = User::with('admin')->where('username',$request->username)->first();
        if($getuser->admin === null){
            return $this->error([
                "message" => "user is not an admin"
            ]);
        }
        $user = new UserLoginResource($getuser);
        return $this->success([
            "user" => $user,
            "token" => $user->createToken("API token for $user->username")->plainTextToken
        ]);
        
    }
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            "message" => "you have been logged out successfully"
        ]);
    }
    public function testupdate(updateUserRequest $request, $user)
    {
        $request->validated($request->all());
        $member = User::where('id', $user);
        //$real_user = User::find($user);
        if ($member->exists()) {
            $real_user = $member->first();
            if ($request->hasFile('profile_pic')) {
                $currentImgName = $real_user->profile_pic;
                $image = $request->file('profile_pic');
                $filePath = $this->UserImageUpload($image, 'profile_images', $currentImgName);
            } else {
                $filePath = $real_user->profile_pic;
            }
      

            $real_user->update([
                'firstname' => isset($request->fullname)?$request->fullname:$real_user->fullname,
                'lastname' => isset($request->lastname)?$request->lastname:$real_user->lastname,
                'middlename' => isset($request->middlename)?$request->middlename:$real_user->middlename,
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
        } else {
            return $this->error([
                'message' => 'member not found'
            ], code: 404);
        }
    }
}
