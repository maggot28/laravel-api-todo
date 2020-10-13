<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Setting\User;

class AuthController extends Controller
{
    public function signup(Request $request) {
        $request->validate([
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        if ($user->save()) {
            $settings = new Setting();
            $settings->options = json_encode(config('user-settings.defaults'));
            $settings->user_id = $user->id;
            $setting->save();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            return response()->json([
                'status' => true,
                'data' => ['token' => $tokenResult->accessToken],
                'message' => 'User created!'
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error on save!'
            ], 201);
        }
    }
  
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'status' => true,
            'data' => ['token' => $tokenResult->accessToken],
            'message' => 'Login Success',
        ]);
    }
  
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }
}