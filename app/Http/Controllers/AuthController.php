<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'mobile_no' => $fields['mobile_no'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Successfully created.',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check Email
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if(!$user||!hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Email or Password are not registered in our system.'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => true,
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully Logout.',
        ]);
    }
}
