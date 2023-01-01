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
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    use HttpResponses;
    public function approveUser (User $member)
    {
       return $this->approve($member,"User");
    }
    public function approveShop (Shop $shop)
    {
        return $this->approve($shop,"Shop");
    }

    public function approveOwner(Owner $owner)
    {
        return $this->approve($owner, "Owner");
    }

    public function approveAttachee(Request $request, Attachee $attachee)
    {
        //return $request->attachee;
        return $this->approve($attachee, "Attachee");
    }
    public function approveApprentice(Apprentice $apprentice)
    {
        //return $request->apprentice;
        return $this->approve($apprentice, "Apprentice");
    }

    public function approveWorker(Worker $worker)
    {
        //return $request->worker;
        return $this->approve($worker, "Worker");
    }
    
    public function approveTaskforce(Taskforce $taskforce)
    {
        return $this->approve($taskforce, "Taskforce");
    }

    private function approve($who, string $message){
        //return 10;
        $who->update([
            'approved' => "1",
        ]);
        return $this->success([
            'status' => 'approved'
        ], "$message has been approved");
    }
}
