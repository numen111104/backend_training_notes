<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required|email",
            'password' => "required|string|min:8"
        ]);

        if ($validator->fails()) {
            return new ApiResource(
                false,
                "Validation errors",
                $validator->errors(),
                422,
                "Validation Error"
            );
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return new ApiResource(
                    false,
                    "User not found, please register first",
                    null,
                    404,
                    "Not Found"
                );
            }
            if (!Hash::check($request->password, $user->password)) {
                return new ApiResource(
                    false,
                    "Invalid Credentials",
                    null,
                    401,
                    "Unauthorized"
                );
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return new ApiResource(
                true,
                "User Login Successfully",
                [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                200,
                "O:"
            );
        } catch (Exception $e) {
            Log::error("Login Error: " . $e->getMessage());
            return new ApiResource(
                false,
                "An Error Occurred during login",
                $e->getMessage(),
                500,
                "Internal Server Error"
            );
        }
    }

    public function register(Request $request)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Validasi request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8'
            ]);

            // Jika validasi gagal, kembalikan pesan error
            if ($validator->fails()) {
                return new ApiResource(
                    false,
                    'Validation errors' . $validator->errors(),
                    $validator->errors(),
                    422,
                    'Validation Error'
                );
            }

            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Ambil user yang baru dibuat
            $user = User::where('email', $request->email)->first();

            // Buat token untuk user baru
            $token = $user->createToken('auth_token')->plainTextToken;

            // Commit transaksi
            DB::commit();

            // Kembalikan respon sukses
            return new ApiResource(
                true,
                'User registered successfully',
                [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                201,
                'Created'
            );
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            // Log error untuk debugging
            Log::error('Registration error: ' . $e->getMessage());

            // Kembalikan respon error
            return new ApiResource(
                false,
                'An error occurred during registration',
                $e->getMessage(),
                500,
                'Internal Server Error'
            );
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return new ApiResource(
                true,
                "User logged out successfully",
                null,
                200,
                "O:"
            );
        } catch (Exception $e) {
            return new ApiResource(
                false,
                "An error occurred during logout",
                $e->getMessage(),
                500,
                "Internal Server Error"
            );
        }
    }
}
