<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;

class TestController extends Controller
{
    public function index()
    {
    	$_user = User::select('*',DB::raw('AES_DECRYPT(userPassword,userToken) as decrypted_pass'))->whereNull('password')->get();
    	foreach($_user as $user)
    	{
    		if(!is_null($user->decrypted_pass))
    		{
    			// dd(bcrypt($user->decrypted_pass));
    			$update = new User;
    			$update->exists = true;
    			$update->userId = $user->userId;
    			$update->password = bcrypt($user->decrypted_pass);
    			$update->save();
    		}
    		
    	}
    }
}
