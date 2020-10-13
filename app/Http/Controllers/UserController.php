<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class UserController extends Controller
{
    public function get()
    {
        $user = Auth::guard('api')->user();
        $formated_settings = json_decode($user->settings->options);
        unset($user->settings);
        $user->settings = $formated_settings;
        return response()->json([
            'status' => true,
            'message' => 'Profile found',
            'data' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('api')->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile settings found',
            'data' => $user->settigs->options,
        ]);
    }

    public function getSettings()
    {
        return response()->json([
            'status' => true,
            'message' => 'Settings found',
            'data' => config('user-settings.options'),
        ]);
    }
}
