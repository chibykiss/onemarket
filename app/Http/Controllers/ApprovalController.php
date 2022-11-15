<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Shop;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    use HttpResponses;
    public function approveUser (User $user)
    {
       return $this->approve($user,"User");
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
