<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ProductModel;
use App\Model\StockModel;

class ItemController extends Controller
{
    public function __construct()
    {

    }

    public function search(Request $request)
    {
    	try
    	{
    		$data = ProductModel::generic()->search($request->keyword)->take(30)->get();
    		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function item_brand(Request $request)
    {
    	try
    	{
    		// dd($request->all());
    		$data = ProductModel::generic()->bybrand($request->brand_identifier)->get();
    		return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function new_arrivals()
    {
        try
        {
            // dd($request->all());
            $data = ProductModel::generic()->where('products.new_arrival','true')->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function list_random(Request $request)
    {
        try
        {
            $pageNumber = intval($request->pageNumber);
            $limit      = 10;
            $offset     = ($limit * $pageNumber) - $limit;
            $data       = ProductModel::generic()->inRandomOrder()->skip($offset)->take($limit)->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function variant(Request $request)
    {
        try
        {
            $data = StockModel::where('product_identifier', $request->product_identifier)->where('stocks_quantity','>',0)->get();
            return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }
}
