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
            'book_reservation.*.max_reserve_days' => 'required|integer',
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

        $qrCodePathArray = [];
        foreach ($qrCodePaths as $qrCodePath) {
            $qrCodePathArray[] = asset($qrCodePath);
        }

        $book_details = [];
        foreach ($reserved_books as $book) {
            $book_details[] = [
                'book_code' => $book['kode_buku'],
                'isbn' => $book['isbn'],
                'book_title' => $book['judul_buku'],
                'book_publisher' => $book['penerbit'],
                'book_stock' => $book['stok'],
                'qty_borrowed' => $book['jumlah_peminjam'],
            ];
        }

        $format_reservation = [];
        foreach ($formattedBookReservation as $res) {
            $format_reservation[] = [
                'reserved_books' => $res['id_detail_reservasi'],
                'reservation_date' => $res['tanggal_reservasi'],
                'reservation_pickup_date' => $res['tanggal_ambil'],
                'reservation_status' => $res['status'],
            ];
        }

        return response()->json([
            'message' => 'Reservation successful',
            'student' => [
                'nim' => $student->nim,
                'student_name' => $student->nama_mhs,
                'email' => $student->email_mhs,
                'major' => $student->prodi_mhs,
                'class' => $student->kelas_mhs,
                'status' => $student->status_mhs,
            ],
            'reservation_data' => $format_reservation,
            'reserved_books' => $book_details,
            'qr_code' => $qrCodePathArray,
        ]);
    }

    public function getReservasi($id_detail_reservasi)
    {
        [$reservasi, $detail_reservasi, $buku, $student] = $this->ReserveBookService->getReservasi($id_detail_reservasi);

        if (!$reservasi || empty($reservasi)) {
            return response()->json(['message' => 'Reservasi not found'], 404);
        }

        $formatReserve = [
            'reservation_code' => $detail_reservasi->id_detail_reservasi,
            'reservation_date' => $reservasi->tanggal_reservasi,
            'pickup_date' => $reservasi->tanggal_ambil,
            'status' => $reservasi->status,
        ];

        $formatBuku = [
            'book_code' => $buku->kode_buku,
            'isbn' => $buku->isbn,
            'book_title' => $buku->judul_buku,
            'publisher' => $buku->penerbit,
            'stock' => $buku->stok,
            'qty_borrowed' => $buku->jumlah_peminjam,
        ];

        $formatStudent = [
            'student_id' => $student->nim,
            'name' => $student->nama_mhs,
            'email' => $student->email_mhs,
            'faculty' => $student->prodi_mhs,
            'class' => $student->kelas_mhs,
            'status' => $student->status_mhs,
        ];



        return response()->json([
            'message' => 'Reservasi found',
            'reservation_data' => $formatReserve,
            'title_book' => $formatBuku,
            'student_data' => $formatStudent,
        ]);
    }


    public function createKonfirmasiReservasi($id_detail_reservasi)
    {
        [$reservasi, $detail_reservasi, $buku] = $this->ReserveBookService->createKonfirmasiReservasi($id_detail_reservasi);

        if (!$reservasi) {
            return response()->json(['message' => 'Reservasi not found'], 404);
        }

        return response()->json([
            'message' => 'Reservation confirmed successfully',
        ]);
    }
}
