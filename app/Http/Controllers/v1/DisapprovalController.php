<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Apprentice;
use App\Models\Attachee;
use App\Models\Owner;
use App\Models\Shop;
use App\Models\Taskforce;
use App\Models\User;
use App\Models\Worker;
use App\Traits\Helpers;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class DisapprovalController extends Controller
{
    use HttpResponses,Helpers;

    public function disapproveUser(User $member)
    {
        return $this->userAssigned($member->id,$member,'Member');
        
    }

    public function disapproveShop(Shop $shop)
    {
        //check if shop has been assigned an owners
        if($shop->owner_id !== null){
            return $this->error(message:"shop cannot be disapproved, it already has an owner");
        }
        return $this->disapprove($shop, "Shop");
    }

    public function disapproveOwner(Owner $owner)
    {
        //check if an owner has been assigned a shop
        $check = Shop::where('owner_id',$owner->id);
        if($check->exists()) return $this->error(message:"Owner cannot be disapproved");
        return $this->disapprove($owner, "Owner");
    }

    public function disapproveAttachee(Attachee $attachee)
    {
        return $this->disapprove($attachee, "Attachee");
    }
    public function disapproveApprentice(Apprentice $apprentice)
    {
        return $this->disapprove($apprentice, "Apprentice");
    }

    public function disapproveWorker(Worker $worker)
    {
        return $this->disapprove($worker, "Worker");
    }

    public function disapproveTaskforce(Taskforce $taskforce)
    {
        return $this->disapprove($taskforce, "Taskforce");
    }

}