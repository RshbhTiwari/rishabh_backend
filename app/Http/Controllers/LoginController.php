<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Cart;
use App\Models\Login;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate the request data with custom messages
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ], [
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'password.required' => 'Password is required',
            ]);
            // Return validation errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }
            // Find the user by email
            $user = User::where('email', $request->email)->first();
            // Check if user exists and password matches
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login credentials',
                ], 401);
            }
            // Generate the auth token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Handle cart merging for logged-in users
            if ($request->has('cart_id')) {
                $cartId = $request->input('cart_id');
                $loginM = new Login();
                $loginM->handleCartMerging($cartId, $user->id);
            }

            $userCart = Cart::where('customer_id', $user->id)->first();
            if ($userCart) {
                $cartId = $userCart->id;
            }
            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'cart_id' => $cartId ?? '',
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke user's token
            $request->user()->tokens()->delete();
            // No need to modify or delete the cart on logout
            return response()->json([
                'status' => true,
                'message' => 'Logout successful ',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Logout failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => __($status),
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 500);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => __($status),
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 500);
    }
}
