<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Setting extends Model
{
    protected $table = 'settings';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'user_id', 'options'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($setting) {
            $setting->id = (string) Uuid::generate(4);
        });
    }
}
