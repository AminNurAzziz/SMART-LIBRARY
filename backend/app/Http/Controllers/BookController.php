<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Student;
use App\Models\BukuPeminjaman;
use App\Http\Services\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $BookService)
    {
        $this->bookService = $BookService;
    }

    public function findBook(Request $request)
    {
        $bookCode = $request->query('kode');

        try {
            $book = $this->bookService->findBookByCode($bookCode);
            $bookArray = [
                'id' => $book->id,
                'book_code' => $book->code_book,
                'isbn' => $book->isbn,
                'book_title' => $book->title_book,
                'publisher' => $book->publisher,
                'author' => $book->code_author,
                'rack_code' => $book->code_rack,
                'stock' => $book->stok,
                'borrower_count' => $book->loan_amount
            ];
            return response()->json($bookArray);
        } catch (ModelNotFoundException $e) {
            Log::warning('Book not found', ['book_code' => $bookCode]);
            return response()->json(['error' => 'Book not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error accessing the database', ['book_code' => $bookCode, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }


    // public function sendStruk(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'kode_pinjam' => 'required|string',
    //     ]);

    //     $email = $request->input('email');
    //     $kode_pinjam = $request->input('kode_pinjam');

    //     $peminjaman = Peminjaman::where('kode_pinjam', $kode_pinjam)->first();

    //     if (!$peminjaman) {
    //         return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
    //     }

    //     $buku_dipinjam = BukuPeminjaman::where('kode_pinjam', $kode_pinjam)->get();

    //     foreach ($buku_dipinjam as $buku) {
    //         // Mengambil path QR code dari direktori lokal
    //         $qrCodePath = public_path('qr_code/' . $buku->id_detail_pinjam . '.png');

    //         $peminjam = Student::where('nim', $peminjaman->nim)->first();
    //         $buku_detail = Buku::where('kode_buku', $buku->kode_buku)->first();

    //         $data_email = [
    //             'subject' => 'SMART LIBRARY',
    //             'sender_name' => 'azzizdev2@gmail.com',
    //             'receiver_email' => $email,
    //             'isi_email' => 'Peminjaman berhasil, silahkan tunjukkan QR Code ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
    //             'data_Borrowing' => $peminjaman,
    //             'buku_detail' => $buku_detail, // Mengirim detail buku yang dipinjam
    //             'peminjam' => $peminjam,
    //         ];

    //         // Menggunakan nama file QR code sebagai attachment
    //         Mail::send(new KirimEmail($data_email, $qrCodePath));
    //     }

    //     return response()->json(['message' => 'Struk berhasil dikirim']);
    // }
}
