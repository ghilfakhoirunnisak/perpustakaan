<?php

namespace App\Http\Controllers;

use App\Models\PenerbitBuku;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PenerbitBukuController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index() {
        return $this->transactionService->handleWithTransaction(function() {
            $penerbitbuku = PenerbitBuku::all();
            return response()->json([
                'success' => true,
                'message' => 'List semua data penerbit buku',
                'data' => $penerbitbuku
            ], 200);
        }, 'list-penerbitbuku');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_penerbit' => 'required',
            'telp' => 'required|max:12',
            'alamat' => 'required',
        ], [
            'nama_penerbit.required' => 'Nama penerbit wajib diisi.',
            'telp.required' => 'Nomor Telepon wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 12 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function() use($request) {
            $data = [
                'nama_penerbit' => $request->nama_penerbit,
                'telp' => $request->telp,
                'alamat' => $request->alamat,
            ];

            $penerbitbuku = PenerbitBuku::create($data);

            $this->transactionService->handleWithLogDB('store-penerbitbuku', 'penerbit_buku', $penerbitbuku->id_penerbit_buku, json_encode($penerbitbuku));
            
            return response()->json([
                'success' => true,
                'message' => 'Penerbit buku berhasil ditambahkan!',
                'data' => $penerbitbuku
            ], 201);
        }, 'store-penerbitbuku');
    }

    public function show($id_penerbit_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_penerbit_buku) {
            $penerbitbuku = PenerbitBuku::where('id_penerbit_buku', $id_penerbit_buku)->first();
            if ($penerbitbuku) {
                return response()->json([
                    'success' => true,
                    'message' => 'List semua penerbit buku',
                    'data' => $penerbitbuku
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerbit buku tidak ditemukan',
                ], 400);
            }
        }, 'detail-penerbitbuku');
    }

    public function update(Request $request, $id_penerbit_buku) {
        $request->validate([
            'nama_penerbit' => 'required',
            'telp' => 'required|max:12',
            'alamat' => 'required',
        ], [
            'nama_penerbit.required' => 'Nama penerbit wajib diisi.',
            'telp.required' => 'Nomor Telepon wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 12 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_penerbit_buku, $request) {
            $penerbitbuku = PenerbitBuku::where('id_penerbit_buku', $id_penerbit_buku)->first();
            if (!$penerbitbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerbit buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'nama_penerbit' => $request->nama_penerbit,
                'telp' => $request->telp,
                'alamat' => $request->alamat,
            ];

            $penerbitbuku->update($data);

            $this->transactionService->handleWithLogDB('update-penerbitbuku', 'penerbit_buku', $penerbitbuku->id_penerbit_buku, json_encode($penerbitbuku));

            return response()->json([
                'success' => true,
                'message' => 'Data penerbit buku berhasil diperbarui!',
                'data' => $penerbitbuku
            ], 200);
        }, 'update-penerbitbuku');
    }

    public function destroy($id_penerbit_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_penerbit_buku) {
            $penerbitbuku = PenerbitBuku::where('id_penerbit_buku', $id_penerbit_buku)->first();
            if (!$penerbitbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerbit buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('delete-penerbitbuku', 'penerbit_buku', $id_penerbit_buku, $penerbitbuku);
            
            $penerbitbuku->delete();
            return response()->json([
                'success' => true,
                'message' => 'Penerbit buku berhasil dihapus!'
            ], 200);
        }, 'delete-penerbitbuku');
    }
}
