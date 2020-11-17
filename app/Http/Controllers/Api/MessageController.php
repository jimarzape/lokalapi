<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Message;

class MessageController extends Controller
{
    public function list(Request $request)
    {
    	$data = Message::where('user_token', $request->user_token)->where('deleted','false')->get();

    	return response()->json($data);
    }

    public function destroy(Request $request)
    {
    	$update = new Message;
    	$update->exists = true;
    	$update->id = $request->id;
    	$update->deleted = 'true';
    	$update->save();
    	return 'success';
    }
}
