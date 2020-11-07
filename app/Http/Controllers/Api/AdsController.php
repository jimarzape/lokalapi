<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\AdsModel;

class AdsController extends Controller
{
    //

    public function index(Request $request)
    {
    	try
    	{
    		if($request->adsType == 'popup')
    		{
    			$data = AdsModel::data($request->adsType)->inRandomOrder()->get();
    		}
    		else
    		{
    			$data = AdsModel::data($request->adsType)->inRandomOrder()->get();
    		}
    		
    		return response()->json($data);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }
}
