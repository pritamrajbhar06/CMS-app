<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        if (!$request->all()) {
            return response()->json(['message' => 'Request body is empty'], 400);
        }

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // Use service to get user
        $user = $this->userService->getApiUserByEmail($request->email);

        if (!$user || Hash::check($request->password, $user->password) === false) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('Personal Access Token')->plainTextToken;


        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}