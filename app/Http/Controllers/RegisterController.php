<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);
        $existinguser = User::where('email', $validatedData['email'])->first();
if($existinguser) {
    return response()->json(['error'=>'Email already in use'], 400);
}
        // Create user after validation
       $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'address' => $validatedData['address'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);
        
        Auth::login($user);
    
        return response()->json(['status' => true, 'message'=> 'Registration success'], 201);
    }
    
}
