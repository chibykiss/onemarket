<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'username',
        'email',
        'phone_number',
        'nationality',
        'sex',
        'marital_status',
        'date_of_birth',
        'profile_pic',
        'approved',
        'user_categories_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routeNotificationForAfricasTalking($notification)
    {
        return $this->phone_number;
    }
    // public function sendPasswordResetNotification($token)
    // {
    //     $url = "http://127.0.0.1:8000/reset-password/$token";
    //     $this->notify(new ResetPasswordNotification($url));
    // }
    public function admin (){
        return $this->hasOne(Admin::class);
    }

    public function owner () {
        return $this->hasOne(Owner::class);
    }

    public function attachee() {
        return $this->hasOne(Attachee::class);
    }

    public function worker(){
        return $this->hasOne(Worker::class);
    }

    public function apprentice(){
        return $this->hasOne(Apprentice::class);
    }

    public function categories (){
        return $this->belongsToMany(UserCategory::class,'user_category_joins','user_id','UserCategory_id');
    }
}
