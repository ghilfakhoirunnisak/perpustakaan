<?php

namespace App\Http\Controllers;

use App\Mail\OTP;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'telp' => 'required|max:13',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|confirmed|min:8',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'telp.required' => 'Nomor telepon wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 13 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $data = [
                'id_role' => 3,
                'nama' => $request->nama,
                'telp' => $request->telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ];

            $user = User::create($data);

            Mail::to($user->email)->send(new OTP($otp));

            return response()->json([
                'success' => true,
                'message' => 'Register berhasil, OTP telah dikirim ke email',
                'data' => $user
            ], 201);
        }, 'register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::with('role')->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            // CEK: Apakah email sudah diverifikasi
            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda dan verifikasi OTP.',
                    'redirect_to' => '/otp'
                ], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->tokens()->latest()->first()->update([
                'expires_at' => now()->addHours(6)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil login.',
                'data' => [
                    'token' => 'Bearer ' . $token,
                    'user' => $user
                ]
            ], 200);
        }, 'Login');
    }

    public function logout(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user();

            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout. Token dihapus.'
            ], 200);
        }, 'Logout');
    }
}
