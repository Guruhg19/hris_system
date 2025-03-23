<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request){
        try{
            // Validate Request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ],);

            // Find user by Email
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error('Unautorized', 401);
            }

            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password)){
                throw new Exception('Invalid Password');
            }

            // Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            // Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login Success');
        }catch(Exception $error){
            return ResponseFormatter::error('Authentication Failed');
        }
    }

    public function register(Request $request){
        try{
            // Validate Request
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required', 'string','email','max:255','unique:users'],
                'password' => ['required', 'string', Password::min(8)->mixedCase()->letters()->numbers()],
            ]);

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Register Success');

        }
        catch(Exception $error){
            // Return Error Response
            return ResponseFormatter::error('Authentication Failed');
        }
    }


    public function logout(Request $request){
        //  Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        //  Return Response
        return ResponseFormatter::success($token,'Logout Success');
    }

    public function fetch(Request $request){
        //  Get User
        $user = $request->user();

        // Return Response
        return ResponseFormatter::success($user, 'User Data Fetched');
    }

}
