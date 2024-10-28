<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact' => $request->contact,
            ]);

            // Generate the auth token
            // $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                // 'access_token' => $token,
                // 'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function addEditUser(string $id)
    {
        $user = $id === 'new' ? new User() : User::findOrFail($id);
        // $roles = Role::get()->pluck('name', 'id')->toArray(); // Assuming you have a Role model

        return view('admin.user-form', ['id' => $id])->with(compact('user'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    public function saveUser(Request $request)
    {
         $data =[
            'name' => $request->name,
            'email' => $request->email,
            'contact'=> $request->contact,
            'pincode'=> $request->pincode,
            'password' => bcrypt($request->password),
            'city' => $request->city,
         ];

        $message = $request->id ? 'User updated' : 'New User added';

        User::updateOrCreate(['id' => $request->id], $data);

        return redirect()->route('users')->with('status', $message . ' successfully!');;

    }
}
