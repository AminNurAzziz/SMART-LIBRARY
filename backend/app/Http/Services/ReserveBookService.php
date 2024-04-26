<?php

namespace App\Http\Services;

use App\Models\BukuReservasi;
use App\Models\ReservasiModel;
use App\Http\Controllers\BorrowingBookController;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;

class ReserveBookService
{
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

        $BorrowingService = new BorrowingBookService();
        $peminjamanController = new BorrowingBookController($BorrowingService);
        $data_request = [
            'buku_pinjam' => [
                [
                    'kode_buku' => $detail_reservasi->kode_buku,
                ],
            ],
        ];
        $request = new \Illuminate\Http\Request($data_request);

        // Tambahkan NIM sebagai header permintaan
        $request->headers->set('nim', $reservasi->nim);

        $response_peminjaman = $peminjamanController->pinjamBuku($request);


        $buku = $detail_reservasi->buku;
        $detail_reservasi->save();

        return [$reservasi, $detail_reservasi, $buku->judul_buku];
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

        return $qrCodePaths;
    }
}
