<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            if (!$request->has('id_reservasi')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID reservasi diperlukan'
                ], 400);
            }

            $dokumen = Dokumen::where('id_reservasi', $request->id_reservasi)->get();

            return response()->json([
                'success' => true,
                'message' => 'List dokumen',
                'data' => $dokumen
            ]);
        }, 'index-dokumen');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_reservasi' => 'required|exists:reservasi_fasilitas,id_reservasi',
            'dokumen.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'id_reservasi.required' => 'ID reservasi wajib diisi.',
            'id_reservasi.exists' => 'Reservasi tidak ditemukan.',
            'dokumen.*.required' => 'File dokumen wajib diisi.',
            'dokumen.*.file' => 'File tidak valid.',
            'dokumen.*.mimes' => 'Format file harus PDF/JPG/PNG.',
            'dokumen.*.max' => 'Ukuran maksimal file 2MB.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $dokumenData = [];

            foreach ($request->file('dokumen') as $file) {
                $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('dokumen', $filename, 'public');

                $dokumen = Dokumen::create([
                    'id_reservasi' => $request->id_reservasi,
                    'nama_file' => $filename,
                    'path_file' => $path,
                ]);

                $dokumenData[] = $dokumen;
            }

            $this->transactionService->handleWithLogDB(
                'store-dokumen',
                'dokumen',
                $request->id_reservasi,
                json_encode($dokumenData)
            );

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'data' => $dokumenData
            ], 201);
        }, 'store-dokumen');
    }

    public function show($id_dokumen)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_dokumen) {
            $dokumen = Dokumen::find($id_dokumen);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail dokumen berhasil diambil.',
                'data' => $dokumen
            ], 200);
        }, 'show-dokumen');
    }

    public function update(Request $request, $id_dokumen)
    {
        $request->validate([
            'dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'dokumen.required' => 'File dokumen wajib diisi.',
            'dokumen.file' => 'File tidak valid.',
            'dokumen.mimes' => 'Format file harus PDF/JPG/PNG.',
            'dokumen.max' => 'Ukuran maksimal file 2MB.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request, $id_dokumen) {
            $dokumen = Dokumen::find($id_dokumen);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            // Hapus file lama jika ada
            if ($dokumen->path_file && Storage::disk('public')->exists($dokumen->path_file)) {
                Storage::disk('public')->delete($dokumen->path_file);
            }

            // Simpan file baru
            $file = $request->file('dokumen');
            $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('dokumen', $filename, 'public'); // simpan di storage/app/public/dokumen

            // Update DB
            $dokumen->update([
                'nama_file' => $filename,
                'path_file' => $path,
            ]);

            $this->transactionService->handleWithLogDB(
                'update-dokumen',
                'dokumen',
                $dokumen->id_dokumen,
                json_encode($dokumen)
            );

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui.',
                'data' => $dokumen
            ], 200);
        }, 'update-dokumen');
    }

    public function updateByReservasi(Request $request, $id_reservasi) {
        $request->validate([
            'dokumen.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request, $id_reservasi) {
            $dokumenLama = Dokumen::where('id_reservasi', $id_reservasi)->get();

            // Hapus semua file lama
            foreach ($dokumenLama as $d) {
                if ($d->path_file && Storage::disk('public')->exists($d->path_file)) {
                    Storage::disk('public')->delete($d->path_file);
                }
                $d->delete();
            }

            // Simpan dokumen baru
            $dokumenBaru = [];
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $file) {
                    $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('dokumen', $filename, 'public');

                    $dokumen = Dokumen::create([
                        'id_reservasi' => $id_reservasi,
                        'nama_file' => $filename,
                        'path_file' => $path
                    ]);

                    $dokumenBaru[] = $dokumen;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui.',
                'data' => $dokumenBaru
            ]);
        }, 'update-dokumen-by-reservasi');
    }

    public function destroy($id_dokumen) {
        return $this->transactionService->handleWithTransaction(function () use ($id_dokumen) {
            $dokumen = Dokumen::find($id_dokumen);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            if ($dokumen->path_file && Storage::disk('public')->exists($dokumen->path_file)) {
                Storage::disk('public')->delete($dokumen->path_file);
            }

            $dokumen->delete();

            $this->transactionService->handleWithLogDB(
                'delete-dokumen',
                'dokumen',
                $id_dokumen,
                json_encode(['deleted' => true])
            );

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.'
            ], 200);
        }, 'delete-dokumen');
    }
}
