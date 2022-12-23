<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Shop;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class DisapprovalController extends Controller
{
    use HttpResponses;

    public function disapproveUser(User $member)
    {
        return $this->disapprove($member, "User");
    }
    public function disapproveShop(Shop $shop)
    {
        return $this->disapprove($shop, "Shop");
    }

    public function disapproveOwner(Owner $owner)
    {
        return $this->disapprove($owner, "Owner");
    }

    private function disapprove($who, string $message)
    {
        //return 10;
        $who->update([
            'approved' => "0",
        ]);
        return $this->success([
            'status' => 'success'
        ], "$message has been disapproved");
    }
}
