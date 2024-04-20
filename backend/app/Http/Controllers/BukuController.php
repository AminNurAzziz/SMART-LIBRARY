<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Student;
use App\Models\BukuPeminjaman;
use App\Http\Services\BukuService;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BukuController extends Controller
{
    protected $bukuService;

    public function __construct(BukuService $bukuService)
    {
        $this->bukuService = $bukuService;
    }

    public function getBuku(Request $request)
    {
        $kode_buku = $request->query('kode');

        try {
            $buku = $this->bukuService->getBukuByKode($kode_buku);
            Log::info('Buku ditemukan', ['kode_buku' => $kode_buku, 'buku' => $buku]);
            return response()->json($buku);
        } catch (ModelNotFoundException $e) {
            Log::warning('Buku tidak ditemukan', ['kode_buku' => $kode_buku]);
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error saat mengakses database', ['kode_buku' => $kode_buku, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }



    public function sendStruk(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'kode_pinjam' => 'required|string',
        ]);

        $email = $request->input('email');
        $kode_pinjam = $request->input('kode_pinjam');

        $peminjaman = Peminjaman::where('kode_pinjam', $kode_pinjam)->first();

        if (!$peminjaman) {
            return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
        }

        $buku_dipinjam = BukuPeminjaman::where('kode_pinjam', $kode_pinjam)->get();

        foreach ($buku_dipinjam as $buku) {
            // Mengambil path QR code dari direktori lokal
            $qrCodePath = public_path('qr_code/' . $buku->id_detail_pinjam . '.png');

            $peminjam = Student::where('nim', $peminjaman->nim)->first();
            $buku_detail = Buku::where('kode_buku', $buku->kode_buku)->first();

            $data_email = [
                'subject' => 'SMART LIBRARY',
                'sender_name' => 'azzizdev2@gmail.com',
                'receiver_email' => $email,
                'isi_email' => 'Peminjaman berhasil, silahkan tunjukkan QR Code ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
                'data_peminjaman' => $peminjaman,
                'buku_detail' => $buku_detail, // Mengirim detail buku yang dipinjam
                'peminjam' => $peminjam,
            ];

            // Menggunakan nama file QR code sebagai attachment
            Mail::send(new KirimEmail($data_email, $qrCodePath));
        }

        return response()->json(['message' => 'Struk berhasil dikirim']);
    }
}
