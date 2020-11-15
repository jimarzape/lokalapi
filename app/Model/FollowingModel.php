<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FollowingModel extends Model
{
    protected $table    = 'following';
    public $timestamps  = false;
    public $primaryKey  = 'id';
}
