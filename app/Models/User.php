<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'updated_at', 'created_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $user->id = (string) Uuid::generate(4);
        });
    }

    function tasks(){
        return $this->hasMany('App\Models\Task', 'user_id', 'id')->orderBy('priority', 'desc')->orderBy('deadline', 'desc');
    }

    function activeTasks(){
        return $this->hasMany('App\Models\Task', 'user_id', 'id')->where('status', 0)->orderBy('deadline', 'asc')->orderBy('priority', 'desc');
    }

    function doneTasks(){
        return $this->hasMany('App\Models\Task', 'user_id', 'id')->where('status', 1)->orderBy('deadline', 'desc');
    }

    function archiveTasks(){
        return $this->hasMany('App\Models\Task', 'user_id', 'id')->onlyTrashed()->orderBy('deadline', 'desc');
    }

    function settings(){
        return $this->hasOne('App\Models\Setting', 'user_id', 'id');
    }

}
