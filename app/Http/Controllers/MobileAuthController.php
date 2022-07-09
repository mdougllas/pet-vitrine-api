<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Verify user info and issue new token
     * Destroy old token if found
     *
     * @param  Illuminate\Http\Request $request
     * @return string
     */
    public function requestToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->where('name', $request['device_name'])->delete();

        return $user->createToken($request->device_name)->plainTextToken;
    }

    /**
     * Destroys Pet Vitrine access token
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function destroyToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
