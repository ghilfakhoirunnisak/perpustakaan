<?php

namespace App\Http\Controllers;

use App\Models\LogApproval;
use App\Models\ReservasiFasilitas;
use App\Models\Verifikator;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class ApprovalFasilitasController extends Controller
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

            if (!$user || !$user->role || $user->role->nama_role !== 'verifikator') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses sebagai verifikator.'
                ], 403);
            }

            $query = ReservasiFasilitas::with(['user', 'fasilitas']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->orderByDesc('created_at')->get();

            return response()->json([
                'success' => true,
                'message' => $request->has('status')
                    ? 'Reservasi dengan status ' . $request->status
                    : 'Semua data reservasi',
                'data' => $data
            ]);
        }, 'verifikator-list-reservasi');
    }

    public function approve(Request $request, $id_reservasi)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request, $id_reservasi) {
            $request->validate([
                'status' => 'required|in:disetujui,ditolak',
                'catatan' => 'nullable|string|max:1000',
            ]);

            $user = $request->user();
            $verifikator = Verifikator::where('id_user', $user->id_user)->first();

            if (!$verifikator) {
                return response()->json(['success' => false, 'message' => 'Kamu bukan verifikator atau data verifikator tidak ditemukan.'], 403);
            }

            $reservasiFasilitas = ReservasiFasilitas::find($id_reservasi);

            if (!$reservasiFasilitas) {
                return response()->json(['success' => false, 'message' => 'Reservasi fasilitas tidak ditemukan.'], 404);
            }

            if ($reservasiFasilitas->status === 'ditolak') {
                return response()->json(['success' => false, 'message' => 'Reservasi ini sudah DITOLAK. Tidak dapat diubah.'], 400);
            }

            if ($reservasiFasilitas->status === 'disetujui') {
                return response()->json(['success' => false, 'message' => 'Reservasi ini sudah DISETUJUI. Tidak dapat diubah.'], 400);
            }

            $sudahApprove = LogApproval::where('id_reservasi', $id_reservasi)
                                        ->where('id_verifikator', $verifikator->id_verifikator)
                                        ->exists();

            if ($sudahApprove) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memberikan keputusan untuk reservasi ini.'], 400);
            }

            $logSaatIni = LogApproval::where('id_reservasi', $id_reservasi)->count();
            if ($reservasiFasilitas->status === 'diproses' && ($logSaatIni + 1 !== $verifikator->level)) {
                return response()->json(['success' => false, 'message' => 'Belum giliran Anda untuk verifikasi atau reservasi sudah diproses verifikator sebelumnya.'], 403);
            }

            LogApproval::create([
                'id_reservasi'   => $id_reservasi,
                'id_verifikator' => $verifikator->id_verifikator,
                'status'         => $request->status,
                'catatan'        => $request->catatan
            ]);

            if ($request->status === 'ditolak') {
                $reservasiFasilitas->update([
                    'status' => 'ditolak',
                    'catatan' => $request->catatan
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Reservasi fasilitas berhasil ditolak.'
                ], 200);
            } else {
                $totalVerifikator = Verifikator::count();
                $totalDisetujui = LogApproval::where('id_reservasi', $id_reservasi)
                                                ->where('status', 'disetujui')
                                                ->count();

                if ($totalDisetujui === $totalVerifikator) {
                    $reservasiFasilitas->update([
                        'status' => 'disetujui'
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Reservasi fasilitas berhasil disetujui.'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Keputusan Anda berhasil dicatat. Menunggu verifikasi dari verifikator selanjutnya.'
                    ], 200);
                }
            }
        }, 'approve-verifikator-reservasi');
    }
}
