<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CancelOrder extends Model
{
    protected $table    = 'cancellation';
    public $timestamps  = true;
    public $primaryKey  = 'id';
}
