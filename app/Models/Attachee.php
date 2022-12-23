<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachee extends Model
{
    use HasFactory;
    //protected $with = ['user']; //to prevent lazy loding

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
