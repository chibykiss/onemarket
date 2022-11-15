<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'tenancy_receipt','reg_receipt', 'cert', 'active', 'approved',
        'owner_served', 'previous_job'
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function shops () {
        return $this->hasMany(Shop::class);
    }
}
