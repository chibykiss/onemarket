<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "shop_number",
        "plaza_name",
        "shop_address",
        "owner_id", 
        "gotten_via",
        "guarantor",
        "known_for",
        "company_name",
        "guranteed",
        "approved",
    ];

    public function owner (){
        return $this->belongsTo(Owner::class);
    }

    public function attachee () {
        return $this->hasMany(Attachee::class);
    }

    public function apprentice(){
        return $this->hasMany(Apprentice::class);
    }

    public function worker(){
        return $this->hasMany(Worker::class);
    }
}
