<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $role = $request->query('role');

            $user = User::with('role')
                ->when($role, function ($query, $role) {
                    $query->whereHas('role', function ($q) use ($role) {
                        $q->where('nama_role', $role);
                    });
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => $role
                    ? "List user dengan role '$role'"
                    : 'List semua data user',
                'data' => $user
            ], 200);
        }, 'list-user');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id_role',
            'nama' => 'required|string|max:100',
            'telp' => 'required|max:13',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:8',
        ], [
            'id_role.required' => 'Role wajib diisi.',
            'id_role.exists' => 'Role tidak ditemukan.',
            'nama.required' => 'Nama wajib diisi.',
            'telp.required' => 'Nomor telepon wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 13 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::create([
                'id_role' => $request->id_role,
                'nama' => $request->nama,
                'telp' => $request->telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            $this->transactionService->handleWithLogDB('store-user', 'user', $user->id_user, json_encode($user));

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan!',
                'data' => $user
            ], 201);
        }, 'store-user');
    }

    public function show(string $id_user)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_user) {
            $user = User::with('role')->where('id_user', $id_user)->first();
            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'List detail user',
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ], 400);
            }
        }, 'detail-user');
    }

    public function update(Request $request, $id_user)
    {
        $request->validate([
            'id_role' => 'required|exists:role,id_role',
            'nama' => 'required',
            'telp' => 'required|max:13',
            'email' => 'required',
            'password' => 'required',
        ], [
            'id_role.required' => 'Role wajib diisi.',
            'id_role.exists' => 'Role tidak ditemukan.',
            'nama.required' => 'Nama wajib diisi.',
            'telp.required' => 'Nomor telepon wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 13 karakter.',
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_user, $request) {
            $user = User::where('id_user', $id_user)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'id_role' => $request->id_role,
                'nama' => $request->nama,
                'telp' => $request->telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

            $user->update($data);

            $this->transactionService->handleWithLogDB('update-user', 'user', $user->id_user, json_encode($user));

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diperbarui!',
                'data' => $user
            ], 200);
        }, 'update-user');
    }

    public function destroy(string $id_user)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_user) {
            $user = User::where('id_user', $id_user)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('delete-user', 'user', $id_user, $user);

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!'
            ], 200);
        }, 'delete-user');
    }

    public function getProfile(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user()->load('role');

            return response()->json([
                'success' => true,
                'user' => [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? null,
                ]
            ], 200);
        }, 'get-profile');
    }
}
