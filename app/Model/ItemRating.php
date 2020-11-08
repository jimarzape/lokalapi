<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemRating extends Model
{
    protected $table    = 'item_rating';
    public $timestamps  = true;
    public $primaryKey  = 'rating_id';

}
