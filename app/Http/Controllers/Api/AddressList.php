<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\AddressList as AddressListModel;

class AddressList extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
    	try
    	{
    		$data = AddressListModel::data($request->provCode)->get();

    		$response = empty($data) ? 'false' : 'true';
    		return response()->json($response);
    	}
    	catch(\Exception $e)
    	{
    		return response()->json(array('message' => $e->getMessage()), 500);
    	}
    }

    public function barangay(Request $request)
    {
        try
        {
            $data = AddressListModel::select('area')->brgy($request->citymunCode)->groupBy('area')->orderBy('area')->get();

            return response()->json($data);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function municipality(Request $request)
    {
        try
        {
            $data = AddressListModel::select('city')->municipality($request->provCode)->groupBy('city')->orderBy('city')->get();

            return response()->json($data);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function province(Request $request)
    {
        try
        {
            $data = AddressListModel::select('province')->groupBy('province')->orderBy('province')->get();

            return response()->json($data);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }
}
