<?php

namespace App\Http\Controllers;

use App\Models\DetailPengajuanBuku;
use App\Models\LogApprovalBuku;
use App\Models\PeminjamanBuku;
use App\Models\PengajuanBuku;
use App\Models\Verifikator;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApprovalBukuController extends Controller
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

            $query = PengajuanBuku::with(['user', 'detail_pengajuan_buku.buku']);

            // Jika ada query status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $pengajuan = $query->orderByDesc('created_at')->get();

            return response()->json([
                'success' => true,
                'message' => $request->has('status')
                    ? 'Data pengajuan dengan status ' . $request->status
                    : 'Semua data pengajuan buku',
                'data' => $pengajuan
            ], 200);
        }, 'verifikator-index-pengajuan-buku');
    }

    public function approve(Request $request, $id_pengajuan_buku)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request, $id_pengajuan_buku) {
            $request->validate([
                'status' => 'required|in:disetujui,ditolak',
                'catatan' => 'nullable|string|max:1000',
            ]);

            $user = $request->user();
            $verifikator = Verifikator::where('id_user', $user->id_user)->first();

            if (!$verifikator) {
                return response()->json(['success' => false, 'message' => 'Kamu bukan verifikator atau data verifikator tidak ditemukan.'], 403);
            }

            $pengajuanBuku = PengajuanBuku::find($id_pengajuan_buku);

            if (!$pengajuanBuku) {
                return response()->json(['success' => false, 'message' => 'Pengajuan buku tidak ditemukan.'], 404);
            }

            if ($pengajuanBuku->status === 'ditolak') {
                return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah DITOLAK. Tidak dapat diubah.'], 400);
            }

            if ($pengajuanBuku->status === 'disetujui') {
                return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah DISETUJUI. Tidak dapat diubah.'], 400);
            }

            $sudahApprove = LogApprovalBuku::where('id_pengajuan_buku', $id_pengajuan_buku)
                                            ->where('id_verifikator', $verifikator->id_verifikator) 
                                            ->exists();

            if ($sudahApprove) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memberikan keputusan untuk pengajuan ini.'], 400);
            }

            $logSaatIni = LogApprovalBuku::where('id_pengajuan_buku', $id_pengajuan_buku)->count();

            // Cek giliran verifikasi hanya jika status pengajuan masih 'diproses'
            if ($pengajuanBuku->status === 'diproses' && ($logSaatIni + 1 !== $verifikator->level)) {
                return response()->json(['success' => false, 'message' => 'Belum giliran Anda untuk verifikasi atau pengajuan sudah diproses verifikator sebelumnya.'], 403);
            }

            LogApprovalBuku::create([
                'id_pengajuan_buku' => $id_pengajuan_buku,
                'id_verifikator'    => $verifikator->id_verifikator,
                'status'            => $request->status,
                'catatan'           => $request->catatan
            ]);

            if ($request->status === 'ditolak') {
                $pengajuanBuku->update([
                    'status' => 'ditolak',
                    'catatan' => $request->catatan
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan buku berhasil ditolak.'
                ], 200);

            } else { // Jika status adalah 'disetujui'
                $totalVerifikator = Verifikator::count();
                $totalDisetujui = LogApprovalBuku::where('id_pengajuan_buku', $id_pengajuan_buku)
                                                    ->where('status', 'disetujui')
                                                    ->count();

                if ($totalDisetujui === $totalVerifikator) {
                    $pengajuanBuku->update(['status' => 'disetujui']);

                    $detailBuku = DetailPengajuanBuku::where('id_pengajuan_buku', $id_pengajuan_buku)->get();
                    $tanggalPinjam = Carbon::now();
                    $tanggalKembali = $tanggalPinjam->copy()->addWeek();

                    foreach ($detailBuku as $item) {
                        PeminjamanBuku::create([
                            'id_user'          => $pengajuanBuku->id_user,
                            'id_buku'          => $item->id_buku,
                            'tanggal_pinjam'   => $tanggalPinjam->toDateString(),
                            'tanggal_kembali'  => $tanggalKembali->toDateString(),
                            'status'           => 'dipinjam'
                        ]);
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Pengajuan buku berhasil disetujui dan peminjaman dibuat.'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Keputusan Anda berhasil dicatat. Menunggu verifikasi dari verifikator selanjutnya.'
                    ], 200);
                }
            }
        }, 'approve-buku-verifikator');
    }

}
