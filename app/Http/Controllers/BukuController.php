<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $buku = Buku::with(['penulis_buku', 'penerbit_buku',])->get();
            return response()->json([
                'success' => true,
                'message' => 'List semua buku',
                'data' => $buku
            ], 200);
        }, 'list-role');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'isbn' => 'nullable|unique:buku,isbn',
            'id_penulis_buku' => 'required|exists:penulis_buku,id_penulis_buku',
            'id_penerbit_buku' => 'required|exists:penerbit_buku,id_penerbit_buku',
            'genre' => 'required',
            'tahun_terbit' => 'required|digits:4|integer',
            'stok' => 'required|integer',
            'sinopsis' => 'required',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'id_penulis_buku.required' => 'Penulis buku wajib dipilih.',
            'id_penulis_buku.exists' => 'Penulis buku yang dipilih tidak valid.',
            'id_penerbit_buku.required' => 'Penerbit buku wajib dipilih.',
            'id_penerbit_buku.exists' => 'Penerbit buku yang dipilih tidak valid.',
            'genre.required' => 'Genre buku wajib dipilih.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari 4 digit.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'sinopsis.required' => 'Sinopsis buku wajib diisi.',
            'cover.image' => 'File cover harus berupa gambar.',
            'cover.mimes' => 'Format gambar cover harus jpeg, png, jpg, atau gif.',
            'cover.max' => 'Ukuran gambar cover maksimal 2MB.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $coverPath = null;

            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('cover', 'public');
            }

            $data = [
                'judul' => $request->judul,
                'isbn' => $request->isbn,
                'id_penulis_buku' => $request->id_penulis_buku,
                'id_penerbit_buku' => $request->id_penerbit_buku,
                'genre' => $request->genre,
                'tahun_terbit' => $request->tahun_terbit,
                'stok' => $request->stok,
                'sinopsis' => $request->sinopsis,
                'cover' => $coverPath,
            ];

            $buku = Buku::create($data);

            $this->transactionService->handleWithLogDB('store-buku', 'buku', $buku->id_buku, json_encode($buku));

            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil ditambahkan!',
                'data' => $buku
            ], 201);
        }, 'store-buku');
    }

    public function show($id_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_buku) {
            $buku = Buku::with(['penulis_buku', 'penerbit_buku'])->where('id_buku', $id_buku)->first();

            if ($buku) {
                return response()->json([
                    'success' => true,
                    'message' => 'Detail buku berhasil ditemukan',
                    'data' => $buku
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data buku tidak ditemukan',
                ], 404);
            }
        }, 'detail-buku');
    }

    public function update(Request $request, $id_buku)
    {
        $request->validate([
            'judul' => 'required',
            'isbn' => 'nullable|unique:buku,isbn,' . $id_buku . ',id_buku',
            'id_penulis_buku' => 'required|exists:penulis_buku,id_penulis_buku',
            'id_penerbit_buku' => 'required|exists:penerbit_buku,id_penerbit_buku',
            'genre' => 'required',
            'tahun_terbit' => 'required|digits:4|integer',
            'stok' => 'required|integer',
            'sinopsis' => 'required',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'id_penulis_buku.required' => 'Penulis buku wajib dipilih.',
            'id_penulis_buku.exists' => 'Penulis buku yang dipilih tidak valid.',
            'id_penerbit_buku.required' => 'Penerbit buku wajib dipilih.',
            'id_penerbit_buku.exists' => 'Penerbit buku yang dipilih tidak valid.',
            'genre.required' => 'Genre buku wajib dipilih.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari 4 digit.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'sinopsis.required' => 'Sinopsis buku wajib diisi.',
            'cover.image' => 'File cover harus berupa gambar.',
            'cover.mimes' => 'Format gambar cover harus jpeg, png, jpg, atau gif.',
            'cover.max' => 'Ukuran gambar cover maksimal 2MB.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_buku, $request) {
            $buku = Buku::find($id_buku);
            if (!$buku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $coverPath = $buku->cover; // default ke cover lama

            // Jika ada file cover baru, hapus yang lama dan simpan yang baru
            if ($request->hasFile('cover')) {
                // Hapus cover lama jika ada
                if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
                    Storage::disk('public')->delete($buku->cover);
                }

                // Simpan cover baru
                $coverPath = $request->file('cover')->store('cover', 'public');
            }

            $data = [
                'judul' => $request->judul,
                'isbn' => $request->isbn,
                'id_penulis_buku' => $request->id_penulis_buku,
                'id_penerbit_buku' => $request->id_penerbit_buku,
                'genre' => $request->genre,
                'tahun_terbit' => $request->tahun_terbit,
                'stok' => $request->stok,
                'sinopsis' => $request->sinopsis,
                'cover' => $coverPath,
            ];

            $buku->update($data);

            $this->transactionService->handleWithLogDB('update-buku', 'buku', $buku->id_buku, json_encode($buku));

            return response()->json([
                'success' => true,
                'message' => 'Data buku berhasil diperbarui!',
                'data' => $buku
            ], 200);
        }, 'update-buku');
    }

    public function destroy($id_buku) {
        return $this->transactionService->handleWithTransaction(function () use ($id_buku) {
            $buku = Buku::where('id_buku', $id_buku)->first();
            if (!$buku) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku dengan ID tersebut tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('delete-buku', 'buku', $id_buku, $buku);
            
            $buku->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data buku berhasil dihapus!'
            ], 200);
        }, 'delete-buku');
    }
}
