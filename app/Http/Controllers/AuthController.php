<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function register(Request $request){   
        
            $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:companies,id',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'required|string',
        ]);

          if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
          }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_id' => $request->company_id,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);

    

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(compact('token'), 200);
    }

    
    public function logout(Request $request)
    {
                    try {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return response()->json(['message' => 'Successfully logged out']);
                } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return response()->json(['error' => 'Failed to logout, token may be invalid'], 500);
                }
    }
    
    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json(compact('token'), 200);
    }
}
