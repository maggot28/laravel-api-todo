<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function get()
    {
        $user = Auth::guard('api')->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile found',
            'data' => $user,
        ]);
    }
}
