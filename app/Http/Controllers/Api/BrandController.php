<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\BrandModel;

class BrandController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
    	try
    	{
    		$data = BrandModel::where('brand_archived', 0)->inRandomOrder()->get();
    		return response()->json($data);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function items()
    {
    	
    }
}
