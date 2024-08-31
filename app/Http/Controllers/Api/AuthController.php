<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Notifications\VerificationCodeNotification;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',

            'phone_number' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:255|unique:users',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $verification_code = rand(100000, 999999);
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verification_code,]);

        // $user->notify(new VerificationCodeNotification($verification_code));
        Log::info('Verification code for ' . $user->phone_number . ': ' . $verification_code);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);}

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }
        if (!$user->is_verified) {
            return response()->json(['error' => 'Account Not Verified'], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
            'email' => 'required|string|email',

            'verification_code' => 'required|integer|min:100000|max:999999',]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('phone_number', $request->phone_number)->where('verification_code', $request->verification_code)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid verification code'], 400);}
        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();
        return response()->json(['message' => 'Account verified successfully'], 200);}


}
