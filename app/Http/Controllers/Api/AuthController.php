<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        $credentials = [
        	'userEmail' => $request->email,
        	'password' => $request->password
        ];
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user           = $request->user();
        $tokenResult    = $user->createToken('Personal Access Token');
        $token          = $tokenResult->token;
        if (true)
            $token->expires_at = Carbon::now()->addMonths(12);
        $token->save();

        $data = Auth::user()->toArray();
        unset($data['userPassword']);
        $data['access_token'] = $tokenResult->accessToken;
        $data['token_type'] = 'Bearer';
        $data['expires_at'] = Carbon::parse( $tokenResult->token->expires_at)->toDateTimeString();
      
        return response()->json($data);
    }

    public function email_checker(Request $request)
    {
        try
        {
            $data = User::where('userEmail', $request->email)->selectRaw('count(*) as countEmail')->get();
            return response()->json($data);
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function logout(Request $request)
    {
        try
        {
            Auth::logout();
            $update['currently_logged_in'] = 'false';
            $update['login_id'] = '';
            User::where('userToken', $request->user_token)->update($update);
            // return response()->json('SUCCESSFUL');
            return 'SUCCESSFUL';
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function session_checker(Request $request)
    {
        try
        {
            $user = User::where('userToken', $request->user_token)->first();
            if(is_null($user))
            {
                return 'user not found';
            }
            else
            {
                return $user->login_id;
            }
        }
        catch(\Exception $e)
        {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    public function password_email_reset(Request $request)
    {
        // $request->email
        $code = mt_rand(100000,999999);
    }

    public function update_password(Request $request)
    {
        $password = bcrypt($request->new_password);
        $user = User::where('userEmail', $request->email)->first();
        if(!is_null($user))
        {
            $update = new User;
            $update->exists = true;
            $update->userId = $user->userId;
            $update->password = $password;
            $update->save();
        }
    }

    public function login_id(Request $request)
    {
        try
        {
            $user = User::where('userToken', $request->user_token)->first();
            if(is_null($user))
            {
                return '';
            }
            else
            {
                return $user->login_id;
            }
        }
        catch(\Exception $e)
        {
            return '';
        }
    }

    public function check_login(Request $request)
    {
        try
        {
            $user = User::selectRaw("IFNULL(currently_logged_in,'false') as 'status'")->where('userEmail', $request->user_email)->first();
            if(is_null($user))
            {
                return 'false';
            }
            else
            {
                return $user->status;
            }
        }
        catch(\Exception $e)
        {
            return 'false';
        }
    }

}
