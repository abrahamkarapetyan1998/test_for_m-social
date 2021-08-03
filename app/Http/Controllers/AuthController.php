<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->all();
        
        $validator = Validator::make($data,[
            'name' => 'string|nullable|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|regex:/^[a-zA-Z]+$/u|max:100|unique:users',
            'password' => 'required|min:5|max:60',
        ]);
        
      
        if ($validator->fails()) {
          
            return response()->json([$validator->messages(), 'status' => 500], 200);
        }
        $user = User::create($data);
        
        $user->password = Hash::make($data['password']);
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
        ]);
    }
   

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => 'Internal Error',
                 'code' => 500,
             ]);
            }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
        ]);
    }

}
