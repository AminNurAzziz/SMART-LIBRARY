<?php

namespace App\Http\Controllers;

use App\Http\Services\ExtendBookService;
use App\Http\Services\BorrowingBookService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\KirimEmailPerpanjangan;
use App\Models\Peminjaman;

class ExtendBookController extends Controller
{
    protected $ExtendBookService;
    protected $BorrowingBookService;

    public function __construct(ExtendBookService $ExtendBookService, BorrowingBookService $BorrowingBookService)
    {
        $this->ExtendBookService = $ExtendBookService;
        $this->BorrowingBookService = $BorrowingBookService;
    }


    public function createPerpanjangan($id_detail_pinjam)
    {

        [$peminjaman, $detail_peminjaman, $id_sebelumnya, $buku] = $this->ExtendBookService->createPerpanjangan($id_detail_pinjam);
        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }
        $data_peminjaman = Peminjaman::where('kode_pinjam', $peminjaman->kode_pinjam)->firstOrFail();
        $data_email = [
            'subject' => 'SMART LIBRARY - Perpanjangan Peminjaman Buku',
            'sender_name' => 'azzizdev2@gmail.com',
            'receiver_email' => $data_peminjaman->student->email_mhs,
            'isi_email' => 'Perpanjangan peminjaman berhasil, berikut QR Code buku yang dipinjam. Tunjukkan ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
            'data_perpanjangan' => $peminjaman,
            'buku_dipinjam' => $detail_peminjaman->buku,
            'peminjam' => $data_peminjaman->student,
        ];

        $formattedBukuPinjam = [
            'id_detail_pinjam' => $detail_peminjaman->id_detail_pinjam,
            'kode_buku' => $detail_peminjaman->kode_buku,
            'id_sebelumnya' => $id_sebelumnya,
        ];
        $qrCodePaths = $this->BorrowingBookService->generateQRCodes([$formattedBukuPinjam]);

        $qrCodePath = $qrCodePaths[0];


        // Log::info('QR Code path: ' . $qrCodePath);
        Log::info('Data Email: ' . json_encode($data_email));
        // Send email
        Mail::to($data_email['receiver_email'])->send(new KirimEmailPerpanjangan($data_email, $qrCodePath));
        Log::info('QR Code path: ' . $qrCodePath);
        Log::info('Data Email: ' . json_encode($peminjaman));
        return response()->json([
            'message' => 'Perpanjangan berhasil',
            'data_perpanjangan' => $peminjaman,
            'judul_buku' => $buku,
            'qr_code' => asset($qrCodePath),
        ]);
    }
}
