<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\BorrowingBookService;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Book;
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
        $buku_dipinjam = Book::whereIn('code_book', array_column($formattedBukuPinjam, 'code_book'))->get();

        foreach ($buku_dipinjam as $buku) {
            $buku->update(['stok' => $buku->stok - 1]);
        }

        foreach ($formattedBukuPinjam as $index => $buku) {
            $singleBukuDetail = $buku_dipinjam->firstWhere('code_book', $buku['code_book']);
            $data_email = [
                'subject' => 'SMART LIBRARY - Peminjaman Buku',
                'sender_email' => 'azzizdev2@gmail.com',
                'receiver_email' => $student->email,
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
                'book_code' => $buku->code_book,
                'isbn' => $buku->isbn,
                'book_title' => $buku->title_book,
                'book_author' => $buku->publisher,
            ];
        }

        $peminjaman_format = [];
        foreach ($formattedBukuPinjam as $buku) {
            $peminjaman_format[] = [
                'borrowed_code' => $buku['loan_detail_id'],
                'borrowed_date' => $buku['loan_date'],
                'return_date' => $buku['return_date'],
                'status' => $buku['status'],
            ];
        }


        return response()->json([
            'message' => 'Peminjaman berhasil',
            'student' => [
                'nim' => $student->nim,
                'student_name' => $student->name,
                'email' => $student->email,
                'major' => $student->major,
                'class' => $student->class,
                'status' => $student->status,
            ],
            'borrowed_data' => $peminjaman_format,
            'borrowed_books' => $buku_dipinjam_formatted,
            'qr_code' => $qrCodePathArray,
        ]);
    }
}
