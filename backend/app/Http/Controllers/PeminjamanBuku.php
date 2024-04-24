<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PeminjamanService;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Student;
use App\Models\Buku;
use Illuminate\Support\Facades\Log;
use App\Models\BukuPeminjaman;
use App\Mail\KirimEmailPerpanjangan;
use App\Mail\KirimEmailReservasi;

class PeminjamanBuku extends Controller
{
    protected $peminjamanService;

    public function __construct(PeminjamanService $peminjamanService)
    {
        $this->peminjamanService = $peminjamanService;
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

        [$peminjaman, $formattedBukuPinjam] = $this->peminjamanService->createPeminjaman($buku_pinjam, $nim);

        $qrCodePaths = $this->peminjamanService->generateQRCodes($formattedBukuPinjam);
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
            Mail::to($student->email)->send(new KirimEmail($data_email, $qrCodePath));
        }

        $qrCodePathArray = [];
        foreach ($qrCodePaths as $qrCodePath) {
            $qrCodePathArray[] = asset($qrCodePath);
        }
        $buku_details = [];
        foreach ($buku_dipinjam as $buku) {
            $buku_details[] = $buku; // Appending to an array
        }

        return response()->json([
            'message' => 'Peminjaman berhasil',
            'data_peminjaman' => $data_email,
            'buku_dipinjam' => $buku_details,
            'qr_code' => $qrCodePathArray,
        ]);
    }

    public function kembaliBuku($id_detail_pinjam)
    {
        $peminjaman = $this->peminjamanService->createPengembalian($id_detail_pinjam);

        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        return response()->json([
            'message' => 'Peminjaman berhasil dikembalikan',
        ]);
    }

    public function getPengembalian($id_detail_pinjam)
    {
        $pengembalian = $this->peminjamanService->getPengembalian($id_detail_pinjam);


        return response()->json([
            'message' => 'Pengembalian found',
            'data_pengembalian' => $pengembalian,
        ]);
    }

    public function createPerpanjangan($id_detail_pinjam)
    {

        [$peminjaman, $detail_peminjaman, $id_sebelumnya, $buku] = $this->peminjamanService->createPerpanjangan($id_detail_pinjam);
        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }
        Log::info('Peminjaman: ' . $peminjaman->id_peminjaman);
        $data_email = [
            'subject' => 'SMART LIBRARY - Perpanjangan Peminjaman Buku',
            'sender_name' => 'azzizdev2@gmail.com',
            'receiver_email' => $peminjaman->student->email_mhs,
            'isi_email' => 'Perpanjangan peminjaman berhasil, berikut QR Code buku yang dipinjam. Tunjukkan ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
            'data_perpanjangan' => $peminjaman,
            'buku_dipinjam' => $detail_peminjaman->buku,
            'peminjam' => $peminjaman->student,
        ];

        $formattedBukuPinjam = [
            'id_detail_pinjam' => $detail_peminjaman->id_detail_pinjam,
            'kode_buku' => $detail_peminjaman->kode_buku,
            'id_sebelumnya' => $id_sebelumnya,
        ];
        $qrCodePaths = $this->peminjamanService->generateQRCodes([$formattedBukuPinjam]);

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
    public function reserveBook(Request $request)
    {
        $nim =  $request->header('nim');
        $request->validate([
            'book_reservation' => 'required|array',
            'book_reservation.*.book_code' => 'required|string',
        ]);

        $book_reservation = $request->input('book_reservation');
        $nim = $request->input('nim');
        $student = Student::where('nim', $nim)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Create reservation
        [$reservation, $formattedBookReservation] = $this->peminjamanService->createReservasi($book_reservation, $nim);

        $qrCodePaths = $this->peminjamanService->generateQRCodesReservasi($formattedBookReservation);

        // Send confirmation email
        $reserved_books = Buku::whereIn('kode_buku', array_column($book_reservation, 'book_code'))->get();

        foreach ($reserved_books as $index => $book) {
            $data_email = [
                'subject' => 'SMART LIBRARY - Book Reservation Confirmation',
                'sender_name' => 'azzizdev2@gmail.com',
                'receiver_email' => $student->email,
                'email_content' => 'Your book reservation has been confirmed. Thank you.',
                'reservation_data' => $reservation,
                'book_detail' => $book,
                'student' => $student,
            ];

            $qrCodePath = $qrCodePaths[$index];
            // Send email
            Mail::to($student->email_mhs)->send(new KirimEmailReservasi($data_email, $qrCodePath));
        }

        $book_details = [];
        foreach ($reserved_books as $book) {
            $book_details[] = $book;
        }

        return response()->json([
            'message' => 'Reservation successful',
            'reservation_data' => $reservation,
            'reserved_books' => $book_details,
        ]);
    }

    public function getReservasi($id_detail_reservasi)
    {
        [$reservasi, $detail_reservasi, $buku] = $this->peminjamanService->getReservasi($id_detail_reservasi);

        if (!$reservasi) {
            return response()->json(['message' => 'Reservasi not found'], 404);
        }

        return response()->json([
            'message' => 'Reservasi found',
            'data_reservasi' => $reservasi,
            'judul_buku' => $buku
        ]);
    }

    public function createKonfirmasiReservasi($id_detail_reservasi)
    {
        [$reservasi, $detail_reservasi, $buku] = $this->peminjamanService->createKonfirmasiReservasi($id_detail_reservasi);
        if (!$reservasi) {
            return response()->json(['message' => 'Reservasi not found'], 404);
        }

        return response()->json([
            'message' => 'Konfirmasi Reservasi berhasil',
        ]);
    }
}
