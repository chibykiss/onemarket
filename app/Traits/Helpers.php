<?php
namespace App\Traits;

use App\Models\User;
use App\Traits\HttpResponses;

trait Helpers {
    use HttpResponses;
    public function isApproved($model){
        if($model->approved == "1"){
            return 'approved';
        }else{
            return 'continue';
        }
    }

    private function isUserApproved($userid){
        $user = User::find($userid);
        if ($user->approved !== "1"){ 
            return $this->error(['message' => 'user is not approved']);
        }
    }
}