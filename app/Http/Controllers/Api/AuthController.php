<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess('Invalid data', $validator->errors()->first());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->responseNotFound('Invalid login credentials', 'The provided credentials are incorrect');
        }

        $user = User::where('email', $request->email)->first();
        $data = [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ];
        return $this->sendApiResponse($data, 'User logged in successfully');
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:users',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess('Invalid data', $validator->errors()->first());
        }

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'email_verified_at' => now(),
                'password' => $request->password,
            ]);

            Order::where('customer_email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);


            $data = [
                'token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ];
            return $this->sendApiResponse($data, 'User registered successfully');
        } catch (\Throwable $th) {
            return $this->responseServerError('Invalid data', $th->getMessage());
        }
    }

    public function getUser(Request $request)
    {
        return $this->sendApiResponse($request->user(), 'User fetched successfully');
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $this->sendApiResponse(null, 'User logged out successfully');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess('Invalid data', $validator->errors()->first());
        }
        if ($request->has('avatar')) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        $user->save();
        return $this->sendApiResponse($user, 'User updated successfully');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess('Invalid data', $validator->errors()->first());
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->responseUnprocess('Invalid current password', 'The current password you entered is incorrect');
        }

        if (Hash::check($request->password, $user->password)) {
            return $this->responseUnprocess('Same password', 'New password must be different from current password');
        }

        try {
            $user->password = $request->password;
            $user->save();

            return $this->sendApiResponse(true, 'Password changed successfully');
        } catch (\Throwable $th) {
            return $this->responseServerError('Server error', $th->getMessage());
        }
    }
}
