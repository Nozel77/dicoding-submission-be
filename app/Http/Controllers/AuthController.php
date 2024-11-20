<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => 'Email already registered',
            ], 409);
        }

        $user = new User($data);
        $user['password'] = Hash::make($data['password']);
        $user['role'] = 'user';
        $user['avatar'] = 'default.png';
        $user['email_verified_at'] = now();
        $user->save();
        $token = $user->createToken('dicoding-submission')->plainTextToken;

        return response()->json([
            'status_code' => 201,
            'message' => 'User registered successfully',
            'data' => new RegisterResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $data = $request->validated();

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = User::where('email', $data['email'])->first();
        $token = $user->createToken('dicoding-submission')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => new LoginResource($user),
            'token' => $token,
        ], 201);

    }

    public function me() : JsonResponse
    {
        $user = Auth::user();

        if (!$user){
            return $this->resUserNotFound();
        }

        return $this->resShowData(new LoginResource($user));
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user){
            auth()->user()->tokens()->delete();
            return $this->resUserLogout();
        }

        return $this->resUserNotFound();
    }

    public function updateAvatar(UserUpdateRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $user = User::find(Auth::id());

        $user->fill($data);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete('public/avatar/'.$user->avatar);
            }

            $imageName = time().'.'.$request->file('avatar')->extension();
            $request->file('avatar')->storeAs('avatar', $imageName, 'public');
            $user->avatar = $imageName;
        }

        $user->save();

        return $this->resUpdatedData(new LoginResource($user));
    }
    }
