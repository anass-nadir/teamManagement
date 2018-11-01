<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function users()
    {

        return $this->belongsToMany('App\User','user_team','user_id','team_id');

    }
}
