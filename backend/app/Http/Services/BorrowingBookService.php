<?php

namespace App\Http\Services;

use App\Models\Student;
use App\Models\Peminjaman;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;

class BorrowingBookService
{
    public function createPeminjaman(array $bukuPinjam, string $nim)
    {
        $formattedBukuPinjam = [];
        foreach ($bukuPinjam as $buku) {
            $formattedBukuPinjam[] = [
                'id_detail_pinjam' => 'KD-P' . $buku['kode_buku'] . Str::random(3),
                'kode_buku' => $buku['kode_buku'],
                'kode_pinjam' => 'P' . time(),
                'tgl_pinjam' => now(),
                'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
                'status' => 'dipinjam',
            ];
        }
        $student = Student::where('nim', '=', $nim)->firstOrFail();
        // $userId = $student->user->user_id;

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'P' . time(),
            'nim' => $nim,
            // 'user_id' => $userId,
            // 'tgl_pinjam' => now(),
            // 'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
            // 'status' => 'dipinjam',
        ]);

        // Associate books with the loan
        $peminjaman->buku()->attach($formattedBukuPinjam);

        return [$peminjaman, $formattedBukuPinjam];
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
}
