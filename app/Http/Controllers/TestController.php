<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;

class TestController extends Controller
{
    public function index()
    {
    	$_user = User::select('*',DB::raw('AES_DECRYPT(userPassword,userToken) as decrypted_pass'))->where('password','')->get();
        // dd($_user);
    	foreach($_user as $user)
    	{
    		if(!is_null($user->decrypted_pass))
    		{
                // dd($user->decrypted_pass);
    			// dd(bcrypt($user->decrypted_pass));
    			$update = new User;
    			$update->exists = true;
    			$update->userId = $user->userId;
    			$update->password = bcrypt($user->decrypted_pass);
    			$update->save();
    		}
    		
    	}
    }

    public function index2()
    {
    	dd(md5(1));
    }
}
