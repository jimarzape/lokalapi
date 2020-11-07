<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StockModel extends Model
{
    protected $table    = 'stocks';
    public $timestamps  = false;
    public $primaryKey  = 'id';
}
