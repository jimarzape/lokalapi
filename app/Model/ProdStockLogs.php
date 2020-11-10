<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProdStockLogs extends Model
{
    protected $table    = 'product_stock_logs';
    public $timestamps  = true;
    public $primaryKey  = 'stock_log_id';
}
