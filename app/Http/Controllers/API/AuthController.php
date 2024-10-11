<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validators = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8'
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return $this->sendError("Invalid credentials", [], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('tokenizer')->plainTextToken;

            return $this->sendResponse("Login successfully", [
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function register(Request $request)
    {
        try {
            $validators = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $data = $request->only(['name', 'email', 'password']);
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);
            $token = $user->createToken('tokenizer')->plainTextToken;

            return $this->sendResponse("Register successfully", [
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }
}
