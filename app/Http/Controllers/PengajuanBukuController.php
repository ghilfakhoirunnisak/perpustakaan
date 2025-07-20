<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBuku;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class PengajuanBukuController extends Controller
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

            $query = PengajuanBuku::with(['detail_pengajuan_buku.buku']);

            // Jika anggota, hanya lihat miliknya
            if ($role === 'anggota') {
                $query->where('id_user', $user->id_user);
            }

            // Admin & Verifikator bisa melihat semua data (tanpa filter)

            $pengajuan = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'List pengajuan buku',
                'data' => $pengajuan
            ], 200);
        }, 'list-pengajuanbuku');
    }

    public function storeByAdmin(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:user,id_user',
            'nama_lengkap' => 'required',
            'alamat' => 'required',
        ], [
            'id_user.required' => 'User wajib dipilih.',
            'nama_lengkap.required' => 'Nama Lengkap wajib dipilih.',
            'alamat.required' => 'Alamat wajib dipilih.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $pengajuanAktif = PengajuanBuku::where('id_user', $request->id_user)
                ->where('status', 'diproses')
                ->first();

            if ($pengajuanAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'User masih memiliki pengajuan pinjam buku yang sedang diproses.',
                ], 422);
            }

            $data = [
                'id_user' => $request->id_user,
                'nama_lengkap' => $request->nama_lengkap,
                'alamat' => $request->alamat,
            ];

            $pengajuanbuku = PengajuanBuku::create($data);

            $this->transactionService->handleWithLogDB('store-pengajuanbuku', 'pengajuan_buku', $pengajuanbuku->id_pengajuan_buku, json_encode($pengajuanbuku));

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pinjam buku berhasil ditambahkan!',
                'data' => $pengajuanbuku
            ], 201);
        }, 'store-pengajuanbuku');
    }

    public function storeByAnggota(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
        ], [
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = auth('sanctum')->user();

            $pengajuanAktif = PengajuanBuku::where('id_user', $user->id_user)
                ->where('status', 'diproses')
                ->first();

            if ($pengajuanAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda masih memiliki pengajuan pinjam buku yang sedang diproses.',
                ], 422);
            }

            $data = [
                'id_user' => $user->id_user,
                'nama_lengkap' => $request->nama_lengkap,
                'alamat' => $request->alamat,
            ];

            $pengajuanbuku = PengajuanBuku::create($data);

            $this->transactionService->handleWithLogDB(
                'store-pengajuanbuku-anggota',
                'pengajuan_buku',
                $pengajuanbuku->id_pengajuan_buku,
                json_encode($pengajuanbuku)
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pinjam buku berhasil diajukan!',
                'data' => $pengajuanbuku
            ], 201);
        }, 'store-pengajuanbuku-anggota');
    }

    public function show($id_pengajuan_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_pengajuan_buku) {
            $pengajuan = PengajuanBuku::with(['user', 'detail_pengajuan_buku.buku'])
                ->where('id_pengajuan_buku', $id_pengajuan_buku)
                ->first();

            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pengajuan ditemukan.',
                'data' => $pengajuan
            ], 200);
        }, 'show-pengajuanbuku');
    }


    public function update(Request $request, $id_pengajuan_buku)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'alamat' => 'required',
        ], [
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_pengajuan_buku, $request) {
            $pengajuanbuku = PengajuanBuku::find($id_pengajuan_buku);

            if (!$pengajuanbuku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan.',
                ], 404);
            }

            $pengajuanbuku->update([
                'nama_lengkap' => $request->nama_lengkap,
                'alamat' => $request->alamat,
            ]);

            $this->transactionService->handleWithLogDB('update-pengajuanbuku', 'pengajuan_buku', $pengajuanbuku->id_pengajuan_buku, json_encode($pengajuanbuku));

            return response()->json([
                'success' => true,
                'message' => 'Data pengajuan berhasil diperbarui.',
                'data' => $pengajuanbuku
            ]);
        }, 'update-pengajuanbuku');
    }

    public function destroy($id_pengajuan_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_pengajuan_buku) {
            $pengajuan = PengajuanBuku::find($id_pengajuan_buku);

            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan.'
                ], 404);
            }

            $pengajuan->delete(); // otomatis akan menghapus detail_pengajuan_buku karena FK cascade

            $this->transactionService->handleWithLogDB(
                'destroy-pengajuanbuku',
                'pengajuan_buku',
                $id_pengajuan_buku,
                json_encode($pengajuan)
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan buku berhasil dihapus.'
            ], 200);
        }, 'destroy-pengajuanbuku');
    }
}
