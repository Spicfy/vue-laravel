<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function check(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

 

        if(Auth::attempt($credentials))
        {
            $user = Auth::user();
            $token = $request->user()->createToken($user->email . 'token')->plainTextToken ;

            $cookie = cookie('jwt', $token, 60*24 );
            return response()->json([
                'status' => true,
                'message' => 'Login Success',
               
            ], 200)->withCookie($cookie);
        }

        return response()->json([
            'status'=> false,
            'message'=> 'Login Failed'
        ], 401);
    }
    public function user(){
        return Auth::user();
    }
}
