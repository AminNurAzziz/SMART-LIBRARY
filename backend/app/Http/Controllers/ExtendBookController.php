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
            'email_content' => 'Perpanjangan peminjaman berhasil, berikut QR Code buku yang dipinjam. Tunjukkan ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
            'extend_data' => $peminjaman,
            'borrowed_books' => $detail_peminjaman->buku,
            'borrower' => $data_peminjaman->student,
        ];

        $formattedBukuPinjam = [
            'id_detail_pinjam' => $detail_peminjaman->id_detail_pinjam,
            'kode_buku' => $detail_peminjaman->kode_buku,
            'id_sebelumnya' => $id_sebelumnya,
        ];
        $qrCodePaths = $this->BorrowingBookService->generateQRCodes([$formattedBukuPinjam]);
        $qrCodePath = $qrCodePaths[0];

        Mail::to($data_email['receiver_email'])->send(new KirimEmailPerpanjangan($data_email, $qrCodePath));

        $formatPeminjaman = [];

        Log::info('Extension successful: ' . $peminjaman);

        // Format the loan extension data
        $formatPeminjaman = [
            'borrow_code' => $peminjaman['id_detail_pinjam'],
            'book_code' => $peminjaman['kode_buku'],
            'borrow_date' => $peminjaman['tgl_pinjam'],
            'return_date' => $peminjaman['tgl_kembali'],
            'status' => $peminjaman['status'],
            'fine' => $peminjaman['denda'],
            'late_days' => $peminjaman['terlambat'],
        ];

        return response()->json([
            'message' => 'Loan extension successful',
            'extension_data' => $formatPeminjaman,
            'title_book' => $buku,
            'student' => [
                'nim' => $data_peminjaman->student->nim,
                'student_name' => $data_peminjaman->student->nama_mhs,
                'email' => $data_peminjaman->student->email_mhs,
                'major' => $data_peminjaman->student->prodi_mhs,
                'class' => $data_peminjaman->student->kelas_mhs,
                'status' => $data_peminjaman->student->status_mhs,
            ],
            'qr_code' => asset($qrCodePath),
        ]);
    }
}
