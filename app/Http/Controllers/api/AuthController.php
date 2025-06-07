<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $req) {
        $req->validate([
            "full_name" => "required",
            "bio" => "required|max:100",
            "username" => "required|min:3|unique:users|regex:/^[a-zA-Z0-9._]+$/",
            "password" => "required|min:6",
            "is_private" => "nullable|boolean"
        ]);

        $user = User::create([
            "full_name" => $req->full_name,
            "bio" => $req->bio,
            "username" => $req->username,
            "password" => Hash::make($req->password),
            "is_private" => $req->is_private ?? false
        ]);

        $token = $user->createToken("facegram")->plainTextToken;

        return Controller::json([
            "message" => "Register Success",
            "token" => $token,
            "user" => $user
        ]);
    }

    public function login(Request $req) {
        $req->validate([
            "username" => "required",
            "password" => "required"
        ]);

       $user = User::whereName($req->username)->first(); 
        if(!$user || !Hash::check($req->password, $user->password)) {
            return Controller::message("Wrong username or password", 401);
        }

        $token = $user->createToken('facegram')->plainTextToken;

        return Controller::json([
            "message" => "Login success",
            "token" => $token,
            "user" => $user
        ]);
    }

    public function logout(Request $req) {
        $user = Auth::user();
        $user->tokens()->delete();

        return Controller::message("Logout Success");
    }
}
