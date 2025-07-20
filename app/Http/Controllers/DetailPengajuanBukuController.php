<?php

namespace App\Http\Controllers;

use App\Models\DetailPengajuanBuku;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class DetailPengajuanBukuController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $detailpengajuanbuku = DetailPengajuanBuku::all();
            return response()->json([
                'success' => true,
                'message' => 'List semua data detail pengajuan pinjam buku',
                'data' => $detailpengajuanbuku
            ], 200);
        }, 'list-detail-pengajuanbuku');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengajuan_buku' => 'required|exists:pengajuan_buku,id_pengajuan_buku',
            'id_buku' => 'required|exists:buku,id_buku',
            'jumlah' => 'required|integer|min:1',
        ], [
            'id_pengajuan_buku.required' => 'Pengajuan buku wajib dipilih.',
            'id_buku.required' => 'Buku wajib dipilih.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            // Cek jika buku yang sama sudah ada di detail pengajuan yang sama
            $existing = DetailPengajuanBuku::where('id_pengajuan_buku', $request->id_pengajuan_buku)
                ->where('id_buku', $request->id_buku)
                ->first();

            if ($existing) {
                // Jika sudah ada, update jumlah
                $existing->jumlah += $request->jumlah;
                $existing->save();

                $message = 'Jumlah buku berhasil diperbarui.';
                $data = $existing;
            } else {
                // Jika belum ada, buat baru
                $data = DetailPengajuanBuku::create([
                    'id_pengajuan_buku' => $request->id_pengajuan_buku,
                    'id_buku' => $request->id_buku,
                    'jumlah' => $request->jumlah,
                ]);

                $message = 'Detail pengajuan buku berhasil ditambahkan.';
            }

            $this->transactionService->handleWithLogDB('store-detailpengajuanbuku', 'detail_pengajuan_buku', $data->id_detail_pengajuan_buku, json_encode($data));

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], 201);
        }, 'store-detailpengajuanbuku');
    }

    public function show($id_pengajuan_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_pengajuan_buku) {
            $details = DetailPengajuanBuku::with('buku')
                ->where('id_pengajuan_buku', $id_pengajuan_buku)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Detail pengajuan ditemukan.',
                'data' => $details
            ], 200);
        }, 'show-detail-by-pengajuan');
    }


    public function update(Request $request, $id_detail_pengajuan_buku)
    {
        $request->validate([
            'id_buku' => 'required|exists:buku,id_buku',
            'jumlah' => 'required|integer|min:1',
        ], [
            'id_buku.required' => 'Buku wajib dipilih.',
            'jumlah.required' => 'Jumlah wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request, $id_detail_pengajuan_buku) {
            $detail = DetailPengajuanBuku::find($id_detail_pengajuan_buku);

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pengajuan tidak ditemukan.',
                ], 404);
            }

            $detail->update([
                'id_buku' => $request->id_buku,
                'jumlah' => $request->jumlah,
            ]);

            $this->transactionService->handleWithLogDB(
                'update-detailpengajuanbuku',
                'detail_pengajuan_buku',
                $detail->id_detail_pengajuan_buku,
                json_encode($detail)
            );

            return response()->json([
                'success' => true,
                'message' => 'Detail pengajuan berhasil diperbarui.',
                'data' => $detail
            ]);
        }, 'update-detailpengajuanbuku');
    }

    public function destroy(string $id_detail_pengajuan_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_detail_pengajuan_buku) {
            $detailpengajuanbuku = DetailPengajuanBuku::where('id_detail_pengajuan_buku', $id_detail_pengajuan_buku)->first();
            if (!$detailpengajuanbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pengajuan dengan ID tersebut tidak ditemukan.'
                ], 404);
            }
            $this->transactionService->handleWithLogDB('delete-detailpengajuanbuku', 'detail_pengajuan_buku', $id_detail_pengajuan_buku, $detailpengajuanbuku);

            $detailpengajuanbuku->delete();

            return response()->json([
                'success' => true,
                'message' => 'Detail pengajuan berhasil dihapus!'
            ], 200);
        }, 'delete-detailpengajuanbuku');
    }
}
