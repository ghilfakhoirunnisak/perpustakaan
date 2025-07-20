<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    
    public function index() {
        return $this->transactionService->handleWithTransaction(function() {
            $fasilitas = Fasilitas::all();
            return response()->json([
                'success' => true,
                'message' => 'List semua data fasilitas',
                'data' => $fasilitas
            ], 200);
        }, 'list-fasilitas');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_fasilitas' => 'required',
            'deskripsi' => 'required',
            'status' => 'in:aktif,nonaktif'
        ], [
            'nama_fasilitas.required' => 'Nama Fasilitas wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'status.in' => 'Status hanya bisa aktif atau nonaktif'
        ]);

        return $this->transactionService->handleWithTransaction(function() use($request) {
            $data = [
                'nama_fasilitas' => $request->nama_fasilitas,
                'deskripsi' => $request->deskripsi,
                'status' => $request->status ?? 'Aktif'
            ];

            $fasilitas = Fasilitas::create($data);

            $this->transactionService->handleWithLogDB('store-fasilitas', 'fasilitas', $fasilitas->id_fasilitas, json_encode($fasilitas));

            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan!',
                'data' => $fasilitas
            ], 201);
        }, 'store-fasilitas');
    }

    public function show($id_fasilitas) {
        return $this->transactionService->handleWithTransaction(function () use ($id_fasilitas) {
            $fasilitas = Fasilitas::where('id_fasilitas', $id_fasilitas)->first();
            if ($fasilitas) {
                return response()->json([
                    'success' => true,
                    'message' => 'List semua fasilitas',
                    'data' => $fasilitas
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas tidak ditemukan',
                ], 400);
            }
        }, 'detail-role');
    }

    public function update(Request $request, $id_fasilitas) {
        $request->validate([
            'nama_fasilitas' => 'required',
            'deskripsi' => 'required',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_fasilitas.required' => 'Nama Fasilitas wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'status.required' => 'Status fasilitas wajib diisi.',
            'status.in' => 'Status hanya bisa aktif atau nonaktif.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_fasilitas, $request) {
            $fasilitas = Fasilitas::where('id_fasilitas', $id_fasilitas)->first();
            if (!$fasilitas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $data = [
                'nama_fasilitas' => $request->nama_fasilitas,
                'deskripsi' => $request->deskripsi,
                'status' => $request->status,
            ];

            $fasilitas->update($data);

            $this->transactionService->handleWithLogDB('update-fasilitas', 'fasilitas', $fasilitas->id_fasilitas, json_encode($fasilitas));

            return response()->json([
                'success' => true,
                'message' => 'Data fasilitas berhasil diperbarui!',
                'data' => $fasilitas
            ], 200);
        }, 'update-fasilitas');
    }

    public function destroy($id_fasilitas) {
        return $this->transactionService->handleWithTransaction(function () use ($id_fasilitas) {
            $fasilitas = Fasilitas::where('id_fasilitas', $id_fasilitas)->first();
            if (!$fasilitas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fasilitas dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $fasilitas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus!'
            ], 200);
        }, 'delete-fasilitas');
    }
}
