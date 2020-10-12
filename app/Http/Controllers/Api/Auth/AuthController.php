<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){

        $validate = $request->validate([
            'name' =>'required|max:25',
            'email' =>'email|required,unique:users',
            'password' =>'required|confirm'
        ]);

        //create user
        $user = new User([
            'name' =>$request->name,
            'email' =>$request->email,
            'password' =>$request->password
        ]);

        $user->save();

        return response()->json($user,201);
    }

    public function login(Request $request){

        $validateData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $login_detail = request(['email','password']);

        if(!Auth::attempt($login_detail)){

            return response()->json([
                'error' =>'Login failed. Please check your detail.'
            ],401
            );
        }

        $user = $request->user();

        $tokenResult = $user->createTocken('AccessToken');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token'=>  "Bearer ".$tokenResult->accessToken,
            'token_id' => $token->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ],200);
    }
}
