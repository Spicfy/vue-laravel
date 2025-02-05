<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request){
        $input = $request->all();

        User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'address' => $input['address'],
            'email'=> $input['email'],
            'password' => Hash::make($input['password'])
        ]);
        return response()->json(['status' => true, 'message'=> 'Registration success']);
    }
}
