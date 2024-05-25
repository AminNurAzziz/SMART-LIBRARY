<?php

namespace App\Http\Controllers;


use App\Http\Services\ReturnBookService;
use App\Mail\KirimEmailInformasiReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmailReservasi;
use App\Models\BukuPeminjaman;
use App\Models\BukuReservasi;
use App\Models\ReservasiModel;
use Illuminate\Support\Facades\Log;

class ReturnBookController extends Controller
{
    protected $ReturnBookService;

    public function __construct(ReturnBookService $ReturnBookService)
    {
        $this->ReturnBookService = $ReturnBookService;
    }


    public function kembaliBuku($id_detail_pinjam)
    {
        $peminjaman = $this->ReturnBookService->createPengembalian($id_detail_pinjam);

        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }
        // Check if the book's stock reaches 0 after returning
        $returned_book = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->first();
        $book = $returned_book->buku;

        if ($book->stok == 1) {
            // Find reservations related to this book that are still waiting
            $related_reservations = BukuReservasi::where('kode_buku', $book->kode_buku)
                ->where('status', 'menunggu')
                ->orderBy('tanggal_reservasi', 'asc')
                ->first();

            Log::info('Related reservation', ['related_reservations' => $related_reservations]);


            // Send notification emails to students with pending reservations
            if ($related_reservations) {
                $reservasi = ReservasiModel::where('kode_reservasi', $related_reservations->kode_reservasi)->first();
                $student = $reservasi->student;
                Log::info('Student', ['student' => $student]);
                $data_email = [
                    'subject' => 'Notification: Book Reservation',
                    'sender_email' => 'azzizdev2@gmail.com',
                    'receiver_email' => $student->email_mhs,
                    'buku_dipinjam' => $book->judul_buku,
                ];

                Log::info('Data email', ['data_email' => $data_email]);



                // Send email
                Mail::to($student->email_mhs)->send(new KirimEmailInformasiReservasi($data_email));

                // Update reservation status
                $related_reservations->status = 'menunggu konfirmasi';
                $related_reservations->save();
            }
        }
        return response()->json([
            'message' => 'Book returned successfully',
        ]);
    }

    public function getPengembalian($id_detail_pinjam)
    {
        $pengembalian = $this->ReturnBookService->getPengembalian($id_detail_pinjam);


        return response()->json([
            'message' => 'Pengembalian found',
            'data_peminjaman' => $pengembalian,
        ]);
    }
}
