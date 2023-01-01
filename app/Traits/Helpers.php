<?php
namespace App\Traits;

use App\Models\User;
use App\Models\UserCategoryJoin;
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

    private function userAssigned($userid, $who, string $message){
        //check if user has been assigned to an entity
        $check = UserCategoryJoin::where('user_id', $userid);
        if ($check->exists()) {
            $check = $check->first();
            switch ($check->UserCategory_id) {
                case 1:
                    $entity = 'Admin';
                    break;
                case 2:
                    $entity = 'Owner';
                    break;
                case 3:
                    $entity = 'Attachee';
                    break;
                case 4:
                    $entity = 'Apprentice';
                    break;
                case 5:
                    $entity = 'Worker';
                    break;
                case 6:
                    $entity = 'Taskforce';
                    break;
                default:
                    $entity = 'Unknown';
            }
            return $this->error(message:"Member is a $entity already");
        }
        return $this->disapprove($who, $message);

        
    }

    private function disapprove($who, string $message)
    {
        $who->update([
            'approved' => "0",
        ]);
        return $this->success([
            'status' => 'success',
        ], "$message has been disapproved");
    }
}