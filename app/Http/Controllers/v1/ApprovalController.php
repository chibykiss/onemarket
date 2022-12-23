<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Shop;
use App\Models\User;
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
