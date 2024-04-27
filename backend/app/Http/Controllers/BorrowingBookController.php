<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\BorrowingBookService;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Student;
use App\Models\Buku;
use Illuminate\Support\Facades\Log;

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
                'sender_email' => 'azzizdev2@gmail.com',
                'receiver_email' => $student->email_mhs,
                'email_content' => 'Peminjaman berhasil, berikut QR Code buku yang dipinjam. Tunjukkan ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
                'borrowed_data' => $peminjaman,
                'borrowed_books' => $buku_dipinjam,
                'book_detail' => $singleBukuDetail,
                'borrower' => $student,
            ];

            $qrCodePath = $qrCodePaths[$index];

            // Send email
            Mail::to($student->email)->send(new KirimEmail($data_email, $qrCodePath));
        }

        $qrCodePathArray = [];
        foreach ($qrCodePaths as $qrCodePath) {
            $qrCodePathArray[] = asset($qrCodePath);
        }

        $buku_dipinjam_formatted = [];
        foreach ($buku_dipinjam as $buku) {
            $buku_dipinjam_formatted[] = [
                'book_code' => $buku->kode_buku,
                'isbn' => $buku->isbn,
                'book_title' => $buku->judul_buku,
                'book_author' => $buku->penerbit,
            ];
        }

        $peminjaman_format = [];
        foreach ($formattedBukuPinjam as $buku) {
            $peminjaman_format[] = [
                'borrowed_code' => $buku['id_detail_pinjam'],
                'borrowed_date' => $buku['tgl_pinjam'],
                'return_date' => $buku['tgl_kembali'],
                'status' => $buku['status'],
            ];
        }


        return response()->json([
            'message' => 'Peminjaman berhasil',
            'student' => [
                'nim' => $student->nim,
                'student_name' => $student->nama_mhs,
                'email' => $student->email_mhs,
                'major' => $student->prodi_mhs,
                'class' => $student->kelas_mhs,
                'status' => $student->status_mhs,
            ],
            'borrowed_data' => $peminjaman_format,
            'borrowed_books' => $buku_dipinjam_formatted,
            'qr_code' => $qrCodePathArray,
        ]);
    }
}
