<?php
namespace App\Traits;

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
}