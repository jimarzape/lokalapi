<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table    = 'messages';
    public $timestamps  = false;
    public $primaryKey  = 'id';
}
