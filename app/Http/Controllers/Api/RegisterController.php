<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class RegisterController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
    	try
    	{

		    $checker_email = User::where('userEmail',$request->userEmail)->exists();
		    $checker_token = User::where('userToken',$request->userToken)->exists();
		    if($checker_email)
		    {
		    	return response()->json(array('message' => 'Email already exists'), 422);
		    }

		    if($checker_email)
		    {
		    	return response()->json(array('message' => 'User Token already exists'), 422);
		    }

    		$user = new User;
			$user->userFullName 		= $request->userFullName;
			$user->userMobile 			= $request->userMobile;
			// $user->userPassword 		= $request->userPassword;
			$user->userImage 			= '';
			$user->userFbId 			= '';
			$user->userGmailId 			= '';
			$user->userAppleId 			= '';
			$user->userBarangay 		= $request->userBarangay;
			$user->userEmail 			= $request->userEmail;
			$user->userCityMunicipality = $request->userCityMunicipality;
			$user->userProvince 		= $request->userProvince;
			$user->userShippingAddress 	= $request->userShippingAddress;
			$user->userToken 			= $request->userToken;
			$user->mapAddress 			= $request->mapAddress;
			$user->password 			= bcrypt($request->userPassword);
			$user->login_id 			= '';
			$user->save();

			return response()->json(array('message' => 'success'), 200);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    	
    }
}
