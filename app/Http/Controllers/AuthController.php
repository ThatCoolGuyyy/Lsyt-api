<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Facades\Utils;
use App\Enums\StatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $requestBody = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|numeric|unique:users',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required'
        ];

        Utils::validate($request->all(), $requestBody);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country
        ]);

        $user->save();

        return Utils::setResponse(
            StatusCode::CREATED, 
            null,
            'User created successfully'
        );
    }

    public function login(Request $request)
    {
        $requestBody = [
            'username' => 'string',
            'email' => 'string|email',
            'password' => 'required|string',
        ];

        Utils::validate($request->all(), $requestBody);

        $user = User::where('email', $request->email)
                ->orWhere('username', $request->username)
                ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Utils::setResponse(
                StatusCode::CREATED, 
                null,
                'Invalid credentials'
            );
        }
            
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        auth()->logout();

        return Utils::setResponse(
            StatusCode::CREATED, 
            null,
            'User logged out successfully'
        );
    }

    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function attachRoleToUser(Request $request)
    {
        $requestBody = [
            'role' => 'string',
            'user_id' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $user = User::find($request->user_id);
        $user->attachRole($request->role);

        return Utils::setResponse(
            StatusCode::CREATED, 
            null,
            'Role attached to user successfully'
        );

    }

    public function recoverPassword(Request $request)
    {
        $requestBody = [
            'email' => 'required',
        ];

        Utils::validate($request->all(), $requestBody);

        $user = User::where('email', $request->email)->first();

        if($user){
            $this->sendPasswordResetEmail($user);
        }

        return Utils::setResponse(
            StatusCode::OK, 
            null,
            'Password reset email sent'
        );
    }

    private function sendPasswordResetEmail($user)
    {
        $url = "http://localhost:3000/reset-password?token";
        $data = [
            'name' => $user->first_name,
            'url' => $url
        ];
        \Mail::send('emails.password-reset', $data, function($message) use ($user) {
            $message->to($user->email, $user->first_name)->subject('Password Reset');
        });
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
