<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\password_reset_pin;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Traits\HttpResponses;
use App\Traits\TermiiSmsApi;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use HttpResponses,TermiiSmsApi;
    //
    public function forgotpassword(Request $request)
    {
        //validate email first 
        if(filter_var($request->emailphone, FILTER_VALIDATE_EMAIL) == true){
            $request->validate([
                "emailphone" => "required|email|exists:users,email"
            ]);
            return $this->sendemailnotification($request->emailphone);
        }elseif(preg_match('/^[0-9]{13}+$/', $request->emailphone)){
            $request->validate([
                "emailphone" => "required|numeric|min:10|exists:users,phone_number"
            ]);
            return $this->sendsmsnotification($request->emailphone);
        }else{
            return $this->error('you did not supply a valid email or phone_number','invalid email or phone number');
        }
    }
    private function sendemailnotification($email)
    {
        password_reset_pin::where('email', $email)->delete();
        $check = User::where('email', $email);
        // Generate random code
        if($check->exists()){
            $code = mt_rand(100000, 999999);
            $user = $check->first();
            //store in db
            password_reset_pin::create([
                'email' => $email,
                'pin' => $code,
                'created_at' => Carbon::now()
            ]);
            Notification::send($user, new ResetPasswordNotification($code));
            return $this->success([
                'status' => 'code is been sent',
                'via' => 'email',
            ], "reset code sent successfully");
        }else{
            return $this->error([
                "status" => "user Deleted"
            ],'this user does not exist',404);
        }
      
      
    }
    private function sendsmsnotification($sms)
    {
        password_reset_pin::where('phone_number', $sms)->delete();
        $user = User::where('phone_number', $sms);
        if($user->exists()){
            $recipient = $user->first();
            $response = $this->sendSms(
            $recipient->phone_number,
            'ALPHANUMERIC',2,60,5,'<1234>',
            'Your Onemarket confirmation code is <1234>. It expires in 10min',
            'N-Alert','dnd',
            );
            //$response = $this->sendSmsApp($recipient,2,10,4,'NUMERIC');
            //return $response->smsStatus;
            if($response->smsStatus == 'Message Sent'){
                password_reset_pin::create([
                    'phone_number' => $recipient->phone_number,
                    'pin_id' => $response->pinId,
                    'created_at' => Carbon::now()
                ]);
                return $this->success([
                    'body' => $response,
                    'via' => 'sms',
                ]);
            }else{
                return $this->error([
                    'body' => $response,
                    'via' => 'sms',
                ]);
            }
           
        }else{
            return $this->error("user has been deleted",'user deleted',404);
        }
        
    }
    public function verifycode(Request $request){
        $request->validate([
            'via' => 'required|string|in:sms,email'
        ]);
        if($request->via === 'email'){
            $request->validate([
                'code' => 'required|string|exists:password_reset_pins,pin',
                'emailsms' => 'required|email|exists:password_reset_pins,email'  
            ]);
            return $this->verifyemail($request->emailsms,$request->code);
        }else if($request->via === 'sms'){
            $request->validate([
                'code' => 'required',
                'emailsms' => 'required|exists:password_reset_pins,phone_number'
            ]);
            return $this->verifysms($request->emailsms,$request->code);
        }
       
    }
    private function verifyemail($email,$pin)
    {
        $check = DB::table('password_reset_pins')->where([
            ['email', $email],
            ['pin', $pin],
        ]);
        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                $check->delete();
                return $this->error([
                    'status' => 'code expired'
                ], 'code expired');
            }
            return $this->success([
                'status' => 'code accepted'
            ], 'accepted');
        }
    }
    private function verifysms($phone,$pin)
    {
        $who = password_reset_pin::where('phone_number', $phone)->first();
        $pin_id = $who->pin_id;
        $response = $this->verifyToken($pin_id,$pin);
        if($response->verified == true){
            $who->update(['pin' => $pin]);
            return $this->success([
                "body" => $response
            ]);
        }else{
            return $this->error([
                "body" => $response
            ]);
        }
       
    }
    public function resetpassword(Request $request)
    {
        $request->validate([
            'via' => 'required|string|in:sms,email'
        ]);
        if($request->via === 'email'){
            $request->validate([
                'emailsms' => 'required|email|exists:password_reset_pins,email',
                'code' => 'required|String|exists:password_reset_pins,pin',
                'password' => 'required|string|min:6|confirmed',
            ]);
            return $this->resetEmailSmsPassword($request->emailsms,$request->code,$request->password,'email');
        }else if($request->via === 'sms'){
            $request->validate([
                'emailsms' => 'required|exists:password_reset_pins,phone_number',
                'code' => 'required|String|exists:password_reset_pins,pin',
                'password' => 'required|string|min:6|confirmed',
            ]);
            return $this->resetEmailSmsPassword($request->emailsms,$request->code,$request->password,'phone_number');
        }
     }
    private function resetEmailSmsPassword($email,$code,$password,$dbcolumn)
    {
        $passwordReset = password_reset_pin::firstWhere([
            ['pin', $code],
            [$dbcolumn, $email],
        ]);
        if ($passwordReset->exists()) {
            $difference = Carbon::now()->diffInSeconds($passwordReset->created_at);
            if ($difference > 3600) {
                $passwordReset->delete();
                return $this->error([
                    'status' => 'code expired'
                ], 'code expired');
            }
            $user = User::where($dbcolumn, $email)->first();
            $user->update([
                'password' => Hash::make($password)
            ]);
            $passwordReset->delete();
            $one_user = new UserResource($user);
            return $this->success([
                'user' => $one_user,
                "token" => $user->createToken('API Token of ' . $user->username)->plainTextToken
            ], 'password reseted successfully');
        }
    }

    // public function forgotpassword(Request $request)
    // {
    //     //validate email first 
    //     $request->validate([
    //         "email" => "required|email"
    //     ]);

    //     $status = Password::sendResetLink($request->only('email')); //send the password reset link
    //     if($status == Password::RESET_LINK_SENT){
    //         return $this->success([
    //             'status' => __($status)
    //         ], "reset link sent successfully");
    //     }else{
    //         return $this->error([
    //             'email' => __($status)
    //         ], "email reset not sent");
    //     }
    // }
    // public function resetpassword(Request $request){
    //     $request->validate([
    //         'email' => 'email|required',
    //         'password' => 'required|confirmed',
    //         'token' => 'required'
    //     ]);

    //     $status = Password::reset(
    //         $request->only('email','password','password_confirmation', 'token'),
    //         function($user) use ($request) {
    //             $user->forceFill([
    //                 'password' => Hash::make($request->password),
    //                 'remember_token' => Str::random(60),
    //             ])->save();

    //             event(new PasswordReset($user));
    //         }
    //     );
    //     if($status === Password::PASSWORD_RESET){
    //       return view('auth.reset-success')->with('status', __($status));
    //     }else{
    //         return back()->withErrors(['email' => [__($status)]]);
    //     }
    // }
}
