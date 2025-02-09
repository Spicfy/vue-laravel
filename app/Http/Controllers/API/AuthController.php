<?php

namespace App\Http\Controllers\API;
  
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

  
class AuthController extends BaseController
{
 
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'address'=> 'required',
            'email' => 'required|email',
            'password' => 'required',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['user'] =  $user;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
  
  
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = Auth::attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
  
        $success = $this->respondWithToken($token);
   

        // Set a cookie with the token
    return $this->sendResponse($success, 'User login successfully.')
    ->cookie('token', $token, config('jwt.ttl') * 60);
    }
  
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function    getUser()
    {
        $success = Auth::user();
   
        return $this->sendResponse($success, 'Refresh token return successfully.');
    }
  
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();


        // Remove the cookie
        $cookie = Cookie::forget('access_token');
        
        return response()->json([
            'success'=> true,
            'message' => 'Successfully logged out.'
        ])->withCookie($cookie);
    }
  
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $success = $this->respondWithToken(Auth::refresh());
   
        return $this->sendResponse($success, 'Refresh token return successfully.');
    }
  
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => config('jwt.ttl') * 60
    ]);
}

/**
 * Change the password
 * 
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function changePassword(Request $request){
    $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_paxsword' => 'required|confirmed',
    ]);
    if($validator->fails()){
        return $this->sendError('Validation Error', $validator->errors(), 400);

    }
    $user = Auth::user();
    if(!Hash::check($request->old_password, $user->password)){
        return $this->sendError('Invalid old password', [], 400);
    }
    $user->password = Hash::make($request->new_password);
    $user->save();
    return $this->sendResponse([], 'Password changed successfully');
}

/**
 * Change user email.
 * 
 * 
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 * 
 */

    public function changeEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email' . Auth::id(),
        ]);
        if($validator->fails()){
            return $this->sendError(
            'Validation Error', $validator->errors(), 400
            );
        }

        $user = Auth::user();
        $user->email = $request->email;
        $user->save();
        return $this->sendResponse([], 'Email changed successfully');
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,email',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return $this->sendResponse([], 'Password reset link sent to your email.');
        } else {
            return $this->sendError('Email Sending Failed', ['error' => __($status)], 500);
        }


        
    }
}
