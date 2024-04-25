<?php

namespace App\Http\Services;

use PgSql\Lob;
use Carbon\Carbon;
use App\Models\Buku;
use Mockery\Undefined;
use App\Models\Student;
use App\Models\Peminjaman;
use App\Models\Regulation;
use Illuminate\Support\Str;
use App\Models\BukuReservasi;
use App\Models\BukuPeminjaman;
use App\Models\ReservasiModel;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\PeminjamanBuku;
use App\Mail\KirimEmailInformasiReservasi;
use Illuminate\Support\Facades\Mail;

class PeminjamanService
{
    public function createPeminjaman(array $bukuPinjam, string $nim)
    {
        $formattedBukuPinjam = [];
        foreach ($bukuPinjam as $buku) {
            $formattedBukuPinjam[] = [
                'id_detail_pinjam' => 'KD-P' . $buku['kode_buku'] . Str::random(3),
                'kode_buku' => $buku['kode_buku'],
                'kode_pinjam' => 'P' . time(),
            ];
        }
        // dd($nim);
        $student = Student::where('nim', '=', $nim)->firstOrFail();
        $userId = $student->user->user_id;
        // dd($userId);

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'P' . time(),
            'nim' => $nim,
            'user_id' => $userId,
            'tgl_pinjam' => now(),
            'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'dipinjam',
        ]);

        // Associate books with the loan
        $peminjaman->buku()->attach($formattedBukuPinjam);

        return [$peminjaman, $formattedBukuPinjam];
    }

    // public function createPengembalian(string $id_detail_pinjam)
    // {
    //     $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
    //     $peminjaman = Peminjaman::where('kode_pinjam', $detail_peminjaman->kode_pinjam)->firstOrFail();
    //     $peminjaman->status = 'dikembalikan';
    //     $buku_pinjam = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->get();
    //     $buku_dipinjam = Buku::whereIn('kode_buku', $buku_pinjam->pluck('kode_buku'))->get();
    //     Log::info('Books borrowed: ' . $peminjaman);
    //     foreach ($buku_dipinjam as $buku) {
    //         $buku->update(['stok' => $buku->stok + 1]);
    //     }

    //     $peminjaman->save();

    //     return $peminjaman;
    // }

    public function getPengembalian(string $id_detail_pinjam)
    {
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
        $peminjaman = $detail_peminjaman->peminjaman;
        $denda = 0;
        $total_keterlambatan = $peminjaman->tgl_kembali < now() ? now()->diffInDays($peminjaman->tgl_kembali) : 0;
        $denda_perhari = Regulation::value('fine_per_day');

        // Pastikan nilai fine_per_day valid sebelum menggunakannya
        if ($denda_perhari !== null) {
            $denda = $total_keterlambatan * $denda_perhari;
        }
        // Ambil semua buku yang dipinjam melalui relasi many-to-many
        $buku_dipinjam = $detail_peminjaman->buku;
        $student = Student::where('nim', $peminjaman->nim)->first();

        $response = [
            'data_peminjaman' => $peminjaman,
            'buku_dipinjam' => $buku_dipinjam->judul_buku,
            'peminjam' => [
                'nim' => $student->nim,
                'nama' => $student->nama_mhs,
            ],
            'keterlambatan' => [
                'total_hari' => $total_keterlambatan,
                'denda' => $denda,
            ]
        ];

        return $response;
    }
    public function createPengembalian(string $id_detail_pinjam)
    {
        // Ambil detail peminjaman berdasarkan id_detail_pinjam
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $id_detail_pinjam)->firstOrFail();
        Log::info('Detail pinjam ditemukan: ' . $detail_peminjaman);

        // Ubah status peminjaman menjadi 'dikembalikan'
        $peminjaman = $detail_peminjaman->peminjaman;
        Log::info('Peminjaman found: ' . $peminjaman);
        $peminjaman->status = 'dikembalikan';
        $denda = 0;
        Log::info('Tanggal kembali: ' . $peminjaman->tgl_kembali);
        $total_keterlambatan = $peminjaman->tgl_kembali < now() ? now()->diffInDays($peminjaman->tgl_kembali) : 0;
        $denda_perhari = Regulation::value('fine_per_day');


        // Pastikan nilai fine_per_day valid sebelum menggunakannya
        if ($denda_perhari !== null) {
            $denda = $total_keterlambatan * $denda_perhari;
        }
        $peminjaman->denda = $denda;
        $peminjaman->terlambat = $total_keterlambatan;

        // Ambil semua buku yang dipinjam melalui relasi many-to-many
        $buku_dipinjam = $detail_peminjaman->buku;
        Log::info('Books borrowed: ' . $buku_dipinjam);

        // Tingkatkan stok untuk setiap buku yang dipinjam
        $buku_dipinjam->each(function ($buku) {
            Log::info('Increasing stock of book: ' . $buku->kode_buku);
            $buku->update(['stok' => $buku->stok + 1]);
        });

        // Simpan perubahan pada status peminjaman
        Log::info('Saving changes to peminjaman');
        $peminjaman->save();

        Log::info('Return of book processed successfully');

        return $peminjaman;
    }

    public function generateQRCodes(array $formattedBukuPinjam)
    {
        $qrCodePaths = [];
        foreach ($formattedBukuPinjam as $buku) {
            // Generate QR code
            $qrCodePath = 'qr_code/' . $buku['id_detail_pinjam'] . '.png';
            $fullPath = public_path($qrCodePath);

            if (!isset($buku['id_sebelumnya'])) {
                $qrCode = QrCode::format('png')->size(300)->generate($buku['id_detail_pinjam']);
            } else {
                $qrCode = QrCode::format('png')->size(300)->color(0, 0, 255)->generate($buku['id_detail_pinjam']);
            }
            file_put_contents($fullPath, $qrCode);

            // Load the QR code image
            $image = Image::make($fullPath);

            // Resize the image to fit within a 350x350 box
            $image->resize(370, 370, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Create a white canvas with dimensions 370x370 (bigger than image for border effect)
            $whiteCanvasImage = Image::canvas(350, 350, '#ffffff');

            // Calculate x and y coordinates to center the image on the canvas
            $x = ($whiteCanvasImage->width() - $image->width()) / 2;
            $y = ($whiteCanvasImage->height() - $image->height()) / 2;

            // Insert the QR code image in the center of the white canvas
            $whiteCanvasImage->insert($image, 'top-left', $x, $y);

            // Save the final image with the border
            $whiteCanvasImage->save($fullPath);

            // Add the path to the array of QR code paths
            $qrCodePaths[] = $qrCodePath;

            // Check if there's an old QR code path and delete it
            if (isset($buku['id_sebelumnya']) && $buku['id_sebelumnya'] !== null) {
                $oldQrCodePath = public_path('qr_code/' . $buku['id_sebelumnya'] . '.png');
                if (file_exists($oldQrCodePath)) {
                    unlink($oldQrCodePath);
                }
            }
        }

        Log::info('QR Code Paths: ' . json_encode($qrCodePaths));
        return $qrCodePaths;
    }

    public function generateQRCodesReservasi(array $formattedBukuReservasi)
    {
        $qrCodePaths = [];
        foreach ($formattedBukuReservasi as $buku) {
            // Generate QR code
            $qrCodePath = 'qr_code/' . $buku['id_detail_reservasi'] . '.png';
            $fullPath = public_path($qrCodePath);

            $qrCode = QrCode::format('png')->size(300)->color(255, 165, 0)->generate($buku['id_detail_reservasi']);
            file_put_contents($fullPath, $qrCode);

            // Load the QR code image
            $image = Image::make($fullPath);

            // Resize the image to fit within a 350x350 box
            $image->resize(370, 370, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Create a white canvas with dimensions 370x370 (bigger than image for border effect)
            $whiteCanvasImage = Image::canvas(350, 350, '#ffffff');

            // Calculate x and y coordinates to center the image on the canvas
            $x = ($whiteCanvasImage->width() - $image->width()) / 2;
            $y = ($whiteCanvasImage->height() - $image->height()) / 2;

            // Insert the QR code image in the center of the white canvas
            $whiteCanvasImage->insert($image, 'top-left', $x, $y);

            // Save the final image with the border
            $whiteCanvasImage->save($fullPath);

            // Add the path to the array of QR code paths
            $qrCodePaths[] = $qrCodePath;

            // Check if there's an old QR code path and delete it
            if (isset($buku['id_sebelumnya']) && $buku['id_sebelumnya'] !== null) {
                $oldQrCodePath = public_path('qr_code/' . $buku['id_sebelumnya'] . '.png');
                if (file_exists($oldQrCodePath)) {
                    unlink($oldQrCodePath);
                }
            }
        }

        Log::info('QR Code Paths: ' . json_encode($qrCodePaths));
        return $qrCodePaths;
    }

    public function createPerpanjangan(string $kodePeminjaman)
    {
        $detail_peminjaman = BukuPeminjaman::where('id_detail_pinjam', $kodePeminjaman)->firstOrFail();
        $peminjaman = $detail_peminjaman->peminjaman;
        $durasi_peminjaman = Regulation::value('max_loan_days');
        $tgl_kembali = Carbon::parse($peminjaman->tgl_kembali)->addDays($durasi_peminjaman);
        $peminjaman->tgl_kembali = $tgl_kembali;
        $peminjaman->save();

        $buku = $detail_peminjaman->buku;
        $id_sebelumnya = $detail_peminjaman->id_detail_pinjam;
        $detail_peminjaman->id_detail_pinjam = 'KD-P' . $buku->kode_buku . Str::random(3);
        // $detail_peminjaman->save();
        Log::info('Detail Peminjamanaaaaaaaaaaaaaaaaaaaaaa: ' . $peminjaman);
        return [$peminjaman, $detail_peminjaman, $id_sebelumnya, $buku->judul_buku];
    }


    public function getPeminjamanByKode($kodePeminjaman)
    {
        return Peminjaman::where('kode_peminjaman', $kodePeminjaman)->firstOrFail();
    }

    public function createReservasi(array $book_reservation, string $nim)
    {
        $formattedBookReservation = [];
        foreach ($book_reservation as $book) {
            $formattedBookReservation[] = [
                'id_detail_reservasi' => 'KD-R' . $book['book_code'] . Str::random(3),
                'kode_buku' => $book['book_code'],
                'kode_reservasi' => 'R' . time(),
            ];
        }

        $reservation = ReservasiModel::create([
            'kode_reservasi' => 'R' . time(),
            'nim' => $nim,
            'tanggal_reservasi' => now(),
            'tanggal_ambil' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'menunggu',
        ]);

        // Associate books with the reservation
        $reservation->buku()->attach($formattedBookReservation);

        return [$reservation, $formattedBookReservation];
    }

    public function getReservasi(string $id_detail_reservasi)
    {
        $detail_reservasi = BukuReservasi::where('id_detail_reservasi', $id_detail_reservasi)->firstOrFail();
        $reservasi = $detail_reservasi->reservasi;
        $buku = $detail_reservasi->buku;

        return [$reservasi, $detail_reservasi, $buku->judul_buku];
    }

    public function createKonfirmasiReservasi(string $id_detail_reservasi)
    {
        $detail_reservasi = BukuReservasi::where('id_detail_reservasi', $id_detail_reservasi)->firstOrFail();
        $reservasi = ReservasiModel::where('kode_reservasi', $detail_reservasi->kode_reservasi)->firstOrFail();
        $reservasi->status = 'diterima';
        $reservasi->save();

        $peminjamanService = new PeminjamanService();
        $peminjamanController = new PeminjamanBuku($peminjamanService);
        $data_request = [
            'buku_pinjam' => [
                [
                    'kode_buku' => $detail_reservasi->kode_buku,
                ],
            ],
            'nim' => $reservasi->nim,
        ];
        $request = new \Illuminate\Http\Request($data_request);

        $response_peminjaman = $peminjamanController->pinjamBuku($request);

        Log::info('Detail Peminjaman: ' . json_decode($response_peminjaman));



        $buku = $detail_reservasi->buku;
        $detail_reservasi->save();

        return [$reservasi, $detail_reservasi, $buku->judul_buku];
    }
}
