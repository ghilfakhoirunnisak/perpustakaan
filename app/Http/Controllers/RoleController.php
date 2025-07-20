<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index() {
        return $this->transactionService->handleWithTransaction(function() {
            $role = Role::all();
            return response()->json([
                'success' => true,
                'message' => 'List semua data role',
                'data' => $role
            ], 200);
        }, 'list-role');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_role' => 'required',
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function() use($request) {
            $data = [
                'nama_role' => $request->nama_role,
            ];

            $role = Role::create($data);

            $this->transactionService->handleWithLogDB('store-role', 'role', $role->id_role, json_encode($role));

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan!',
                'data' => $role
            ], 201);
        }, 'store-role');
    }

    public function show(string $id_role) {
        return $this->transactionService->handleWithTransaction(function () use ($id_role) {
            $role = Role::where('id_role', $id_role)->first();

            if ($role) {
                return response()->json([
                    'success' => true,
                    'message' => 'List semua role',
                    'data' => $role
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak ditemukan',
                ], 400);
            }
        }, 'detail-role');
    }

    public function update(Request $request, string $id_role) {
        $request->validate([
            'nama_role' => 'required',
        ], [
            'nama_role.required' => 'Nama role wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_role, $request) {
            $role = Role::where('id_role', $id_role)->first();
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'nama_role' => $request->nama_role,
            ];

            $role->update($data);

            $this->transactionService->handleWithLogDB('update-role', 'role', $role->id_role, json_encode($role));

            return response()->json([
                'success' => true,
                'message' => 'Data role berhasil diperbarui!',
                'data' => $role
            ], 200);
        }, 'update-role');
    }

    public function destroy(string $id_role) {
        return $this->transactionService->handleWithTransaction(function () use ($id_role) {
            $role = Role::where('id_role', $id_role)->first();
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dengan ID tersebut tidak ditemukan.'
                ], 404);
            }
            $this->transactionService->handleWithLogDB('delete-role', 'role', $id_role, $role);

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus!'
            ], 200);
        }, 'delete-role');
    }
}
