<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeUserRequest;
use App\Http\Requests\updateUserRequest;
use App\Http\Resources\UserResource;
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
        $user = new User();
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $filePath = $this->UserImageUpload($image, 'profile_images');
        } else {
            $filePath = "noimage.jpg";
        }
        //$trim_num = ltrim($request->phone_number,'0');
        $user->profile_pic = $filePath;
        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->user_categories_id = $request->userCategory_id;
        $user->password = Hash::make(strtolower($request->password));
        $user->save();
        return $this->success([
            "user" => $user,
            //"token" => $user->createToken('API Token of '.$user->username)->plainTextToken
        ]);
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

        $user = User::where('username',$request->username)->first();
        return $this->success([
            "user" => $user,
            "token" => $user->createToken("API token for ".$user->username)->plainTextToken
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
                'fullname' => isset($request->fullname)?$request->fullname:$real_user->fullname,
                'username' => isset($request->username) ? $request->username : $real_user->username,
                'profile_pic' => $filePath,
                'email' => isset($request->email) ? $request->email : $real_user->email,
                'phone_number' => isset($request->phone_number) ? $request->phone_number : $real_user->phone_number,
                'user_categories_id' => isset($request->user_type) ? $request->user_type : $real_user->user_categories_id,
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
