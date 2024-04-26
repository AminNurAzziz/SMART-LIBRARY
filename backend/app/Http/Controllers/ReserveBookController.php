<?php

namespace App\Http\Controllers;


use App\Http\Services\ReserveBookService;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmailReservasi;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Buku;
use Illuminate\Support\Facades\Log;

class ReserveBookController extends Controller
{
    protected $ReserveBookService;

    public function __construct(ReserveBookService $ReserveBookService)
    {
        $this->ReserveBookService = $ReserveBookService;
    }

    public function reserveBook(Request $request)
    {
        Log::info('Reserve book request: ' . json_encode($request->all()));
        $request->validate([
            'book_reservation' => 'required|array',
            'book_reservation.*.book_code' => 'required|string',
        ]);
        Log::info('Reserve book request: ' . json_encode($request->all()));
        $nim =  $request->header('nim');
        $book_reservation = $request->input('book_reservation');
        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Create reservation
        [$reservation, $formattedBookReservation] = $this->ReserveBookService->createReservasi($book_reservation, $nim);

        $qrCodePaths = $this->ReserveBookService->generateQRCodesReservasi($formattedBookReservation);

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
        [$reservasi, $detail_reservasi, $buku] = $this->ReserveBookService->getReservasi($id_detail_reservasi);

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
        [$reservasi, $detail_reservasi, $buku] = $this->ReserveBookService->createKonfirmasiReservasi($id_detail_reservasi);
        if (!$reservasi) {
            return response()->json(['message' => 'Reservasi not found'], 404);
        }

        return response()->json([
            'message' => 'Konfirmasi Reservasi berhasil',
        ]);
    }
}
