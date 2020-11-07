<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductModel extends Model
{
    protected $table    = 'products';
    public $timestamps  = false;
    public $primaryKey  = 'product_id';

    public function scopesearch($query, $search)
    {
    	return $query->where('products.product_name','LIKE','%'.$search.'%')
    		  // ->groupBy('products.product_id')
    		  ->orderBy('products.product_name');
    }

    public function scopegeneric($query)
    {
        return $query->select('products.*', 
            'item_rating.rating',
            'item_rating.comment',
            'item_rating.rating_date',
            'item_rating.rating_id',
            'is_on_sale.sale_price',
            // DB::raw("IFNULL(ROUND(SUM(item_rating.rating)/COUNT(item_rating.rating),1),0) as 'product_rating'"),
            DB::raw("0 as 'product_rating'"),
            DB::raw("IFNULL(is_on_sale.sale_price,0.00) as 'product_sale_price'"))
              ->leftjoin('item_rating','item_rating.product_id','products.product_id')
              ->leftjoin('is_on_sale','is_on_sale.product_id','products.product_id')
              ->where('products.product_active', 1)
              ->where('products.product_archived', 0);
    }

    public function scopebybrand($query, $brand_identifier)
    {
        return $query->where('products.brand_identifier', $brand_identifier);
    }
}
