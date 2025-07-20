<?php

namespace App\Http\Controllers;

use App\Models\Verifikator;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class VerifikatorController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    
    public function index() {
        return $this->transactionService->handleWithTransaction(function() {
            $verifikator = Verifikator::with('user')->get();
            return response()->json([
                'success' => true,
                'message' => 'List semua data verifikator',
                'data' => $verifikator
            ], 200);
        }, 'list-verifikator');
    }

    public function store(Request $request) {
        $request->validate([
            'id_user' => 'required|exists:user,id_user',
            'level' => 'required',
            'jabatan' => 'required',
            'status' => 'in:aktif,nonaktif'
        ], [
            'id_user.required'  => 'User wajib diisi',
            'id_user.exists' => 'User tidak ditemukan',
            'level.required' => 'Level wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'status.in' => 'Status hanya bisa aktif atau nonaktif'
        ]);

        return $this->transactionService->handleWithTransaction(function() use($request) {
            $data = [
                'id_user' => $request->id_user,
                'level' => $request->level,
                'jabatan' => $request->jabatan,
                'status' => $request->status,
            ];

            $verifikator = Verifikator::create($data);
            
            $this->transactionService->handleWithLogDB('store-verifikator', 'verifikator', $verifikator->id_verifikator, json_encode($verifikator));

            return response()->json([
                'success' => true,
                'message' => 'Verifikator berhasil ditambahkan!',
                'data' => $verifikator
            ], 201);
        }, 'store-verifikator');
    }

    public function show($id_verifikator) {
        return $this->transactionService->handleWithTransaction(function () use ($id_verifikator) {
            $verifikator = Verifikator::with('user')->where('id_verifikator', $id_verifikator)->first();
            if ($verifikator) {
                return response()->json([
                    'success' => true,
                    'message' => 'List semua verifikator',
                    'data' => $verifikator
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator tidak ditemukan',
                ], 400);
            }
        }, 'detail-verifikator');
    }

    public function update(Request $request, $id_verifikator) {
        $request->validate([
            'level' => 'required',
            'jabatan' => 'required',
            'status' => 'in:aktif,nonaktif'
        ], [
            'level.required' => 'Level wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'status.in' => 'Status hanya bisa aktif atau nonaktif'
        ]);
    
        return $this->transactionService->handleWithTransaction(function () use ($id_verifikator, $request) {
            $verifikator = Verifikator::with('user')->where('id_verifikator', $id_verifikator)->first();
            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'level' => $request->level,
                'jabatan' => $request->jabatan,
                'status' => $request->status,
            ];

            $verifikator->update($data);
            
            $this->transactionService->handleWithLogDB('update-verifikator', 'verifikator', $verifikator->id_verifikator, json_encode($verifikator));

            return response()->json([
                'success' => true,
                'message' => 'Data verifikator berhasil diperbarui!',
                'data' => $verifikator
            ], 200);
        }, 'update-verifikator');
    }

    public function destroy(string $id_verifikator) {
        return $this->transactionService->handleWithTransaction(function () use ($id_verifikator) {
            $verifikator = Verifikator::with('user')->where('id_verifikator', $id_verifikator)->first();
            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('delete-verifikator', 'user', $id_verifikator, $verifikator);

            $verifikator->delete();
            return response()->json([
                'success' => true,
                'message' => 'Verifikator berhasil dihapus!'
            ], 200);
        }, 'delete-verifikator');
    }
}
