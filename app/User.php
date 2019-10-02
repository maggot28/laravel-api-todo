<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'updated_at', 'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
        return $this->hasMany('App\Task', 'user_id', 'id')->orderBy('priority', 'desc')->orderBy('deadline', 'desc');
    }

    function activeTasks(){
        return $this->hasMany('App\Task', 'user_id', 'id')->where('status', 0)->orderBy('priority', 'desc')->orderBy('deadline', 'desc');
    }

    function doneTasks(){
        return $this->hasMany('App\Task', 'user_id', 'id')->where('status', 1)->orderBy('deadline', 'desc');
    }

    function archiveTasks(){
        return $this->hasMany('App\Task', 'user_id', 'id')->onlyTrashed()->orderBy('deadline', 'desc');
    }

}
