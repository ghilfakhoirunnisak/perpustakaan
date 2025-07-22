<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanBuku;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user();
            $role = $user->role->nama_role;

            $query = PeminjamanBuku::with(['user', 'buku']);

            if ($role === 'anggota') {
                $query->where('id_user', $user->id_user);
            }

            $peminjaman = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'List peminjaman buku',
                'data' => $peminjaman
            ], 200);
        }, 'list-peminjaman');
    }

    public function show($id_peminjaman_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_peminjaman_buku) {
            $peminjaman = PeminjamanBuku::with(['user', 'buku'])
                ->where('id_peminjaman_buku', $id_peminjaman_buku)
                ->first();

            if (!$peminjaman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data peminjaman tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail peminjaman ditemukan.',
                'data' => $peminjaman
            ], 200);
        }, 'show-peminjamanbuku');
    }
}
