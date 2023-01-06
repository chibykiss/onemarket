<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;


    public function payers(){
        return $this->belongsToMany(UserCategory::class,'bill_usercategories','bill_id','userCategory_id');
    }
}
