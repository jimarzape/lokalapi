<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use App\Model\Cart;
use App\Mail\Test as TestEmail;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function index2()
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

    public function index()
    {
    	// dd(md5(1));
        // return order_number();
        $_cart = Cart::leftjoin('products','products.product_identifier','cart.product_identifier')
                     ->leftjoin('users','users.userToken','cart.userToken')
                     ->whereNull('cart.product_id')
                     ->get();
        foreach($_cart as $cart)
        {
            // dd($cart);
            $update                 = new Cart;
            $update->exists         = true;
            $update->cart_id        = $cart->cart_id;
            $update->product_id     = $cart->product_id;
            $update->user_id        = $cart->userId;
            $update->save();
        }
    }

    public function email()
    {
        $params['from'] = 'order@lokaldatph.com';
        Mail::to('jimarzape@gmail.com')->send(new TestEmail($params)); 

        // return view('mails.order');
    }
}
