<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class password_reset_pin extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'email',
        'phone_number',
        'pin',
        'pin_id',
        'created_at',
    ];
}
