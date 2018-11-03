<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'subject','place','synthesis'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User','user_interview','interview_id','user_id');
    }
}
