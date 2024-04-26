<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\BorrowingBookService;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Student;
use App\Models\Buku;

class BorrowingBookController extends Controller
{
    protected $BorrowingService;

    public function __construct(BorrowingBookService $BorrowingService)
    {
        $this->BorrowingService = $BorrowingService;
    }

    public function pinjamBuku(Request $request)
    {
        $nim =  $request->header('nim');
        $request->validate([
            'buku_pinjam' => 'required|array',
            'buku_pinjam.*.kode_buku' => 'required|string',
        ]);

        $buku_pinjam = $request->input('buku_pinjam');

        $student = Student::where('nim', $nim)->first();


        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        [$peminjaman, $formattedBukuPinjam] = $this->BorrowingService->createPeminjaman($buku_pinjam, $nim);

        $qrCodePaths = $this->BorrowingService->generateQRCodes($formattedBukuPinjam);
        $buku_dipinjam = Buku::whereIn('kode_buku', array_column($formattedBukuPinjam, 'kode_buku'))->get();

        foreach ($buku_dipinjam as $buku) {
            $buku->update(['stok' => $buku->stok - 1]);
        }

        foreach ($formattedBukuPinjam as $index => $buku) {
            $singleBukuDetail = $buku_dipinjam->firstWhere('kode_buku', $buku['kode_buku']);
            $data_email = [
                'subject' => 'SMART LIBRARY - Peminjaman Buku',
                'sender_name' => 'azzizdev2@gmail.com',
                'receiver_email' => $student->email_mhs,
                'isi_email' => 'Peminjaman berhasil, berikut QR Code buku yang dipinjam. Tunjukkan ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
                'data_peminjaman' => $peminjaman,
                'buku_dipinjam' => $buku_dipinjam,
                'buku_detail' => $singleBukuDetail,
                'peminjam' => $student,
            ];

            $qrCodePath = $qrCodePaths[$index];

            // Send email
            // Mail::to($student->email)->send(new KirimEmail($data_email, $qrCodePath));
        }

        $qrCodePathArray = [];
        foreach ($qrCodePaths as $qrCodePath) {
            $qrCodePathArray[] = asset($qrCodePath);
        }

        return response()->json([
            'message' => 'Peminjaman berhasil',
            'peminjam' => $student,
            'buku_dipinjam' => $buku_dipinjam,
            'peminjaman' => $peminjaman,
            'qr_code' => $qrCodePathArray,
        ]);
    }
}
