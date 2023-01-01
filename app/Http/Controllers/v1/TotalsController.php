<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Apprentice;
use App\Models\Attachee;
use App\Models\User;
use App\Models\Worker;
use App\Traits\HttpResponses;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class TotalsController extends Controller
{
    use HttpResponses;
    public function totalMembers(){
        $users = User::all()->count();
        return $this->success(['total' => $users]);
    }

    public function totalAttachees(){
        $attachees = Attachee::all()->count();
        return $this->success(['total' => $attachees]);
    }

    public function totalApprentices(){
        $apprentices = Apprentice::all()->count();
        return $this->success(['total' => $apprentices]);
    
    }

    public function totalWorkers(){
        $workers = Worker::all()->count();
        return $this->success(['total' => $workers]);
    }

}
