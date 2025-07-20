<?php

namespace App\Http\Controllers;

use App\Models\PenulisBuku;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PenulisBukuController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index() {
        return $this->transactionService->handleWithTransaction(function() {
            $penulisbuku = PenulisBuku::all();
            return response()->json([
                'success' => true,
                'message' => 'List semua data penulis buku',
                'data' => $penulisbuku
            ], 200);
        }, 'list-penulisbuku');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_penulis' => 'required',
            'negara' => 'required',
        ], [
            'nama_penulis.required' => 'Nama penulis wajib diisi.',
            'negara.required' => 'Negara wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function() use($request) {
            $data = [
                'nama_penulis' => $request->nama_penulis,
                'negara' => $request->negara,
            ];

            $penulisbuku = PenulisBuku::create($data);

            $this->transactionService->handleWithLogDB('store-penulisbuku', 'penulis_buku', $penulisbuku->id_penulis_buku, json_encode($penulisbuku));
            
            return response()->json([
                'success' => true,
                'message' => 'Penulis buku berhasil ditambahkan!',
                'data' => $penulisbuku
            ], 201);
        
        }, 'store-penulisbuku');
    }

    public function show($id_penulis_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_penulis_buku) {
            $penulisbuku = PenulisBuku::where('id_penulis_buku', $id_penulis_buku)->first();
            if ($penulisbuku) {
                return response()->json([
                    'success' => true,
                    'message' => 'List semua penulis buku',
                    'data' => $penulisbuku
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Penulis buku tidak ditemukan',
                ], 400);
            }
        }, 'detail-penulisbuku');
    }

    public function update(Request $request, $id_penulis_buku) {
        $request->validate([
            'nama_penulis' => 'required',
            'negara' => 'required',
        ], [
            'nama_penulis.required' => 'Nama penulis wajib diisi.',
            'negara.required' => 'Negara wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_penulis_buku, $request) {
            $penulisbuku = PenulisBuku::where('id_penulis_buku', $id_penulis_buku)->first();
            if (!$penulisbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penulis buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'nama_penulis' => $request->nama_penulis,
                'negara' => $request->negara,
            ];

            $penulisbuku->update($data);

            $this->transactionService->handleWithLogDB('update-penulisbuku', 'penulis_buku', $penulisbuku->id_penulis_buku, json_encode($penulisbuku));

            return response()->json([
                'success' => true,
                'message' => 'Data penulis buku berhasil diperbarui!',
                'data' => $penulisbuku
            ], 200);
        }, 'update-penulisbuku');
    }

    public function destroy($id_penulis_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_penulis_buku) {
            $penulisbuku = PenulisBuku::where('id_penulis_buku', $id_penulis_buku)->first();
            if (!$penulisbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penulis buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('delete-penulisbuku', 'penulis_buku', $id_penulis_buku, $penulisbuku);
            
            $penulisbuku->delete();
            return response()->json([
                'success' => true,
                'message' => 'Penulis buku berhasil dihapus!'
            ], 200);
        }, 'delete-penulisbuku');
    }
}
