<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\FollowingModel;

class FollowerController extends Controller
{
    public function index(Request $request)
    {
    	
    	$data['num_of_followers'] = 0;
    	try
    	{
    		$data['num_of_followers'] = FollowingModel::where('brand_identifier', $request->brand_identifier)->where('is_following','true')->count();

    	}
    	catch(\Exception $e)
    	{

    	}

    	return response()->json($data);
    }

    public function follow(Request $request)
    {
    	$response['result'] = 'failed';
    	try
    	{
    		$checker = FollowingModel::where('brand_identifier', $request->brand_identifier)
    							->where('user_token', $request->user_token)
    							->first();

	    	$follow = new FollowingModel;
	    	if(!is_null($checker))
	    	{
	    		$follow->exists = true;
	    		$follow->id 	= $checker->id;	
	    	}
	    	$follow->user_token 		= $request->user_token;
	    	$follow->brand_identifier 	= $request->brand_identifier;
	    	$follow->is_following 		= $request->transact == 1 ? 'true' : 'false';
	    	$follow->save();
	    	$response['result'] 		= 'success';
    	}
    	catch(\Exception $e)
    	{

    	}

    	return response()->json($response);
    	
    }

    public function check(Request $request)
    {
    	$data['result'] = 'false';
    	try
    	{
    		$result = FollowingModel::where('brand_identifier', $request->brand_identifier)
    										->where('user_token', $request->user_token)
    										->where('is_following','true')
    										->exists();
    		$data['result'] = $result ? 'true' : 'false';
    	}
    	catch(\Exception $e)
    	{

    	}

    	return response()->json($data);
    }
}
