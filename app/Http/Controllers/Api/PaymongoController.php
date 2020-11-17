<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class PaymongoController extends Controller
{
    public function index()
    {

    }

    public function source(Request $request)
    {
    	try
    	{
    		$user = User::where('userToken', $request->user_token)->where('userEmail', $request->user_email)->first();
	    	if(!is_null($user))
	    	{
	    		$amount_due = ($request->amount_due * 100);
	    		$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/sources');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);

				$data = array(
	                'data' =>
	                    array(
	                        'attributes' =>
	                            array(
	                                'amount' => $amount_due,
	                                'billing' => array(
	                                    'email' => $request->user_email,
	                                    'name' => $request->user_name,
	                                    'phone' => $request->user_phone,
	                                    'address' => array (
	                                        'line1' => $request->user_address,
	                                    ),
	                                ),
	                                'redirect' =>
	                                    array(
	                                        'success' => route('payment_status').'?response=SUCCESS',
	                                        'failed' => route('payment_status').'?response=FAILED',
	                                    ),
	                                'type' => 'gcash',
	                                'currency' => 'PHP',
	                            ),
	                    ),
	            );
	            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	            if (curl_errno($ch)) 
	            {
               	 	return 'Error:' . curl_error($ch);
	            } 
	            else 
	            {
	                return $result;
	            }

            	curl_close($ch);
	    	}
	    	else
	    	{
	    		return response('Not Authorized', 401);
	    	}
    	}
    	catch(\Exception $e)
    	{
    		return response('Not Authorized', 401);
    	}
    	
    	
    }

    public function status(Request $request)
    {
    	try
    	{
    		return $request->response;
    	}
    	catch(\Exception $e)
    	{
    		return 'UNKNOWN';
    	}
    }

    public function payment_intent(Request $request)
    {
    	try
    	{
    		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_intents');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$data = array (
			    'data' =>
			        array (
			            'attributes' =>
			                array (
			                    'amount' => 10000,
			                    'payment_method_allowed' =>
			                        array (
			                            0 => 'card',
			                        ),
			                    'payment_method_options' =>
			                        array (
			                            'card' =>
			                                array (
			                                    'request_three_d_secure' => 'any',
			                                ),
			                        ),
			                    'currency' => 'PHP',
			                    'description' => 'O34878738748',
			                    'statement_descriptor' => 'LokaldatPH',
			                ),
			        ),
			);

			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			$headers = array();
			$headers[] = 'Authorization: Basic ' . base64_encode('sk_test_Pyy7QsfBeTumuz4G3bctHFxh:');
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			if (curl_errno($ch)) 
			{
			    return 'Error:' . curl_error($ch);
			}
			else{
			    return $result;
			}
			curl_close($ch);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json($e->getMessage(), 500);
    	}
    }

    public function method(Request $request)
    {
    	try
    	{
    		$user = User::where('userToken', $request->user_token)->first();
	    	if(!is_null($user))
	    	{
	    		$ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_methods');
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($ch, CURLOPT_POST, 1);
		        $data=array (
		            'data' =>
		                array (
		                    'attributes' =>
		                        array (
		                            'details' =>
		                                array (
		                                    'card_number' => '4343434343434345',
		                                    'exp_month' => 12,
		                                    'exp_year' => 2030,
		                                    'cvc' => '434',
		                                ),
		                            'billing' =>
		                                array (
		                                    'address' =>
		                                        array (
		                                            'line1' => 'Hello, pasih',
		                                            'country' => 'PH',
		                                        ),
		                                    'name' => 'Ysay buna',
		                                    'email' => 'paulchristian1996@gmail.com',
		                                    'phone' => '09173086398',
		                                ),
		                            'type' => 'card',
		                        ),
		                ),
		        );
		        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		        $headers = array();
		        $headers[] = 'Authorization: Basic cGtfdGVzdF9lTXFudDN2Q3B0Wml3WE5wMzNDbWlLUDk6';
		        $headers[] = 'Content-Type: application/json';
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		        $result = curl_exec($ch);
		        if (curl_errno($ch)) 
		        {
		            return 'Error:' . curl_error($ch);
		        }
		        curl_close($ch);
	    	}
	    	else
	    	{
	    		return response('Not Authorized', 401);
	    	}
    	}
    	catch(\Exception $e)
    	{
    		return response('Not Authorized', 401);
    	}
    }
}
