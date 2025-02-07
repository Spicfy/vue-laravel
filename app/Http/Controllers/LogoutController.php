<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function logout(){
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'Logout successful'
        ])->withCookie($cookie);
    }
}
