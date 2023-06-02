<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'Success',
                'message' => 'Register berhasil',
                'data' => $token
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(), 
                'old-request' => $request->all()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('username', 'password'))) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Username atau password salah'
                ], Response::HTTP_UNAUTHORIZED);
            }
            $token = auth()->user()->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'Success',
                'message' => 'Login berhasil',
                'data' => $token
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(), 
                'old-request' => $request->all()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Logout berhasil'
        ], Response::HTTP_OK);
    }
}
