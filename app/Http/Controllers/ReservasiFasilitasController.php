<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\ReservasiFasilitas;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReservasiFasilitasController extends Controller
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

            // Cek jika user dan relasi role tersedia
            if (!$user || !$user->role) {
                return response()->json([
                    'success' => false,
                    'message' => 'User atau role tidak valid.',
                ], 403);
            }

            $query = ReservasiFasilitas::with(['user', 'fasilitas']);

            // Jika user adalah anggota, hanya tampilkan miliknya
            if ($user->role->nama_role === 'anggota') {
                $query->where('id_user', $user->id_user);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => $user->role->nama_role === 'anggota'
                    ? 'List reservasi milik Anda'
                    : 'List semua data reservasi',
                'data' => $data
            ], 200);
        }, 'list-reservasifasilitas');
    }

    public function storeByAdmin(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:user,id_user',
            'id_fasilitas' => 'required|exists:fasilitas,id_fasilitas',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_kegiatan',
            'keterangan' => 'nullable|string',
        ], [
            'id_user.required' => 'User wajib dipilih.',
            'id_fasilitas.required' => 'Fasilitas wajib dipilih.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::with('role')->where('id_user', $request->id_user)->first();

            if (!$user || $user->role->nama_role !== 'anggota') {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang dipilih bukan anggota.'
                ], 422);
            }

            // Cek jika sudah ada reservasi aktif (diproses)
            $reservasiproses = ReservasiFasilitas::where('id_user', $request->id_user)
                ->where('status', 'diproses')
                ->first();

            if ($reservasiproses) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini sudah memiliki reservasi yang sedang diproses.'
                ], 409);
            }

            $kode = 'RSV-' . mt_rand(100000, 999999);

            $data = [
                'kode_reservasi' => $kode,
                'id_user' => $request->id_user,
                'id_fasilitas' => $request->id_fasilitas,
                'tanggal_kegiatan' => $request->tanggal_kegiatan,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
                'status' => 'diproses',
            ];

            $reservasi = ReservasiFasilitas::create($data);

            // Logging
            $this->transactionService->handleWithLogDB('store-reservasi-admin', 'reservasi_fasilitas', $reservasi->id_reservasi, json_encode($reservasi));

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibuat!',
                'data' => $reservasi
            ], 201);
        }, 'store-reservasi-admin');
    }

    public function storeByAnggota(Request $request) {
        $request->validate([
            'id_fasilitas' => 'required|exists:fasilitas,id_fasilitas',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_kegiatan',
            'keterangan' => 'nullable|string',
        ], [
            'id_fasilitas.required' => 'Fasilitas wajib dipilih.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user();

            if (!$user || $user->role->nama_role !== 'anggota') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya anggota yang dapat melakukan reservasi.'
                ], 403);
            }

            $existing = ReservasiFasilitas::where('id_user', $user->id_user)
                ->where('status', 'diproses')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu masih memiliki reservasi yang sedang diproses.'
                ], 409);
            }

            $kode = 'RSV-' . mt_rand(100000, 999999);

            $data = [
                'kode_reservasi' => $kode,
                'id_user' => $user->id_user,
                'id_fasilitas' => $request->id_fasilitas,
                'tanggal_kegiatan' => $request->tanggal_kegiatan,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
                'status' => 'diproses',
            ];

            $reservasi = ReservasiFasilitas::create($data);

            $this->transactionService->handleWithLogDB(
                'store-reservasi-anggota',
                'reservasi_fasilitas',
                $reservasi->id_reservasi,
                json_encode($reservasi)
            );

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil diajukan!',
                'data' => $reservasi
            ], 201);
        }, 'store-reservasi-anggota');
    }


    public function show($id_reservasi)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_reservasi) {
            $reservasi = ReservasiFasilitas::with(['user', 'fasilitas', 'dokumen'])->where('id_reservasi', $id_reservasi)->first();
            if ($reservasi) {
                return response()->json([
                    'success' => true,
                    'message' => 'List detail data reservasi fasilitas',
                    'data' => $reservasi
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan',
                ], 400);
            }
        }, 'show-reservasifasilitas');
    }

    public function updateByAdmin(Request $request, $id_reservasi)
    {
        $request->validate([
            'id_fasilitas' => 'required|exists:fasilitas,id_fasilitas',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_kegiatan',
            'keterangan' => 'nullable|string',
        ], [
            'id_user.required' => 'User wajib dipilih.',
            'id_fasilitas.required' => 'Fasilitas wajib dipilih.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_reservasi, $request) {
            $reservasi = ReservasiFasilitas::where('id_reservasi', $id_reservasi)->first();
            if (!$reservasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan.'
                ], 404);
            }

            $data = [
                'id_fasilitas' => $request->id_fasilitas,
                'tanggal_kegiatan' => $request->tanggal_kegiatan,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
            ];

            $reservasi->update($data);

            $this->transactionService->handleWithLogDB('updateByAdmin-reservasifasilitas', 'reservasi_fasilitas', $reservasi->id_reservasi, json_encode($reservasi));

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil diperbarui.',
                'data' => $reservasi
            ], 200);
        }, 'updateByAdmin-reservasifasilitas');
    }

    public function destroy($id_reservasi)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_reservasi) {
            $reservasi = ReservasiFasilitas::with('dokumen')->where('id_reservasi', $id_reservasi)->first();

            if (!$reservasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan.'
                ], 404);
            }

            // Hapus semua dokumen terkait dari storage dan database
            foreach ($reservasi->dokumen as $dokumen) {
                if ($dokumen->path_file && Storage::disk('public')->exists($dokumen->path_file)) {
                    Storage::disk('public')->delete($dokumen->path_file);
                }
                $dokumen->delete();
            }

            // Hapus reservasi
            $reservasi->delete();

            // Logging
            $this->transactionService->handleWithLogDB(
                'delete-reservasi',
                'reservasi_fasilitas',
                $id_reservasi,
                json_encode(['id_reservasi' => $id_reservasi])
            );

            return response()->json([
                'success' => true,
                'message' => 'Reservasi dan dokumen terkait berhasil dihapus.'
            ], 200);
        }, 'delete-reservasi');
    }
}
