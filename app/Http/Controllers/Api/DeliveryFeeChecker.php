<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryFeeChecker extends Controller
{
    public function mr_speedy(Request $request)
    {
    	try
    	{
    		$curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, 'https://robotapitest.mrspeedy.ph/api/business/1.1/calculate-order');
	        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	        curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-DV-Auth-Token: 6C1B10F6EF77A03340E73C4B264772864B73FE19']);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	        $data = [
	            'matter' => 'TShirts',
	            'total_weight_kg' => $request->weight,
	            'points' => [
	                [
	                    'address' => $request->from_address,
	                ],
	                [
	                    'address' => $request->address,
	                ],
	            ],
	        ];

	        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	        $result = curl_exec($curl);
	        if (!$result) {
	            throw new \Exception(curl_error($curl), curl_errno($curl));
	        }
	        return $result;
    	}
    	catch(\Exception $e)
    	{
    		return 'SERVER ERROR';
    	}
    	
    }
}
