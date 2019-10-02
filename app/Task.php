<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'tasks';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'description', 'priority', 'deadline'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($task) {
            $task->id = (string) Uuid::generate(4);
        });
    }
}
