<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Claims\Subject;
use App\Mail\KirimEmail;
use App\Models\Student;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use App\Models\BukuPeminjaman;


class BukuController extends Controller
{

    public function getBuku(Request $request)
    {
        $kode_buku = $request->query('kode');
        $buku = Buku::where('kode_buku', $kode_buku)->first();

        // Periksa apakah $buku tidak null sebelum mengembalikan respons
        if ($buku) {
            // Log informasi debug
            Log::info('Buku: ' . json_encode($buku));

            // Kembalikan data buku dalam bentuk JSON
            return response()->json($buku);
        } else {
            // Jika buku tidak ditemukan, kembalikan respons yang sesuai
            Log::warning('Buku tidak ditemukan untuk kode: ' . $kode_buku);
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        }
    }


    public function pinjamBuku(Request $request)
    {
        $request->validate([
            'buku_pinjam' => 'required|array',
            'buku_pinjam.*.kode_buku' => 'required|string',
            'nim' => 'required|string',
        ]);

        $buku_pinjam = $request->input('buku_pinjam');
        $nim = $request->input('nim');

        // Menyusun ulang array $buku_pinjam sehingga 'kode_buku' muncul sebelum 'kode_pinjam'
        $formatted_buku_pinjam = [];
        foreach ($buku_pinjam as $buku) {
            $formatted_buku_pinjam[] = [
                'id_detail_pinjam' => 'KD-P' . $buku['kode_buku'] . Str::random(3),
                'kode_buku' => $buku['kode_buku'],
                'kode_pinjam' => 'P' . time(),
            ];
        }

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'P' . time(),
            'nim' => $nim,
            'tgl_pinjam' => now(),
            'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'dipinjam',
        ]);

        // Menggunakan $formatted_buku_pinjam yang sudah disusun ulang
        $peminjaman->buku()->attach($formatted_buku_pinjam);

        $peminjam = Student::where('nim', $nim)->first();
        $buku_dipinjam = Buku::whereIn('kode_buku', array_column($formatted_buku_pinjam, 'kode_buku'))->get();

        foreach ($formatted_buku_pinjam as $buku) {
            $qrCodeUrl = QrCode::size(300)
                ->format('png')
                ->generate($peminjaman->kode_pinjam);

            // Generate QR code
            $qrCode = QrCode::format('png')->size(300)->generate($buku['id_detail_pinjam']);

            // Path untuk menyimpan gambar QR code
            $qrCodePath = 'qr_code/' . $buku['id_detail_pinjam'] . '.png';
            // Simpan QR code sebagai file sementara
            file_put_contents(public_path($qrCodePath), $qrCode);

            // Buka gambar QR code dengan Intervention Image
            $image = Image::make(public_path($qrCodePath));

            $canvas_image = $image;

            $image->resize(370, 370, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $image->save();

            $canvas_image->resize(350, 350, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            // Buat canvas putih
            $whiteCanvasImage = Image::canvas(350, 350, '#ffffff');
            // Hitung posisi x dan y untuk menempatkan gambar QR code di tengah canvas putih
            $x = ($whiteCanvasImage->width() - $canvas_image->width()) / 2;
            $y = ($whiteCanvasImage->height() - $canvas_image->height()) / 2;

            // Masukkan gambar QR code ke tengah canvas putih
            $whiteCanvasImage->insert($canvas_image, 'top-left', $x, $y);
            // Simpan gambar QR code dengan border
            $whiteCanvasImage->save(public_path($qrCodePath));

            $buku = Buku::where('kode_buku', $buku['kode_buku'])->first();



            $data_email = [
                'subject' => 'SMART LIBRARY',
                'sender_name' => 'azzizdev2@gmail.com',
                'receiver_email' => $peminjam->email_mhs,
                'isi_email' => 'Peminjaman berhasil, silahkan tunjukkan QR Code ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
                'data_peminjaman' => $peminjaman,
                'buku_dipinjam' => $buku_dipinjam,
                'buku_detail' => $buku, // Mengirim detail buku yang dipinjam
                'peminjam' => $peminjam,
            ];

            Log::info('Data Email: ' . json_encode($data_email));


            Mail::send(new KirimEmail($data_email, $qrCodePath));
        }

        $qrCodePathArray = [];
        foreach ($formatted_buku_pinjam as $buku) {
            $qrCodePathArray[] = 'qr_code/' . $buku['id_detail_pinjam'] . '.png';
        }



        // Kirim variabel $data_email ke tampilan
        return view('success_pinjam', compact('data_email'), compact('qrCodePathArray'));
    }

    public function sendStruk(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'kode_pinjam' => 'required|string',
        ]);

        $email = $request->input('email');
        $kode_pinjam = $request->input('kode_pinjam');

        $peminjaman = Peminjaman::where('kode_pinjam', $kode_pinjam)->first();

        if (!$peminjaman) {
            return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
        }

        $buku_dipinjam = BukuPeminjaman::where('kode_pinjam', $kode_pinjam)->get();

        foreach ($buku_dipinjam as $buku) {
            // Mengambil path QR code dari direktori lokal
            $qrCodePath = public_path('qr_code/' . $buku->id_detail_pinjam . '.png');

            $peminjam = Student::where('nim', $peminjaman->nim)->first();
            $buku_detail = Buku::where('kode_buku', $buku->kode_buku)->first();

            $data_email = [
                'subject' => 'SMART LIBRARY',
                'sender_name' => 'azzizdev2@gmail.com',
                'receiver_email' => $email,
                'isi_email' => 'Peminjaman berhasil, silahkan tunjukkan QR Code ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
                'data_peminjaman' => $peminjaman,
                'buku_detail' => $buku_detail, // Mengirim detail buku yang dipinjam
                'peminjam' => $peminjam,
            ];

            // Menggunakan nama file QR code sebagai attachment
            Mail::send(new KirimEmail($data_email, $qrCodePath));
        }

        return response()->json(['message' => 'Struk berhasil dikirim']);
    }
}



    // public function pinjamBuku(Request $request)
    // {
    //     $request->validate([
    //         'buku_pinjam' => 'required|array',
    //         'buku_pinjam.*.kode_buku' => 'required|string',
    //         'nim' => 'required|string',
    //     ]);

    //     $buku_pinjam = $request->input('buku_pinjam');
    //     $nim = $request->input('nim');

    //     // Menyusun ulang array $buku_pinjam sehingga 'kode_buku' muncul sebelum 'kode_pinjam'
    //     $formatted_buku_pinjam = [];
    //     foreach ($buku_pinjam as $buku) {
    //         $formatted_buku_pinjam[] = [
    //             'kode_buku' => $buku['kode_buku'],
    //             'kode_pinjam' => 'P' . time(),
    //         ];
    //     }

    //     $peminjaman = Peminjaman::create([
    //         'kode_pinjam' => 'P' . time(),
    //         'nim' => $nim,
    //         'tgl_pinjam' => now(),
    //         'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
    //         'status' => 'dipinjam',
    //     ]);

    //     // Menggunakan $formatted_buku_pinjam yang sudah disusun ulang
    //     $peminjaman->buku()->attach($formatted_buku_pinjam);

    //     // Menghasilkan QR Code
    //     $qrCode = QrCode::generate($peminjaman->id);


    //     return response()->json([
    //         'message' => 'Peminjaman berhasil',
    //         'kode_pinjam' => $peminjaman->kode_pinjam,
    //         'qr_code' => $qrCode,
    //     ]);
    // }

    // public function pinjamBuku(Request $request)
    // {
    //     $request->validate([
    //         'buku_pinjam' => 'required|array',
    //         'buku_pinjam.*.kode_buku' => 'required|string',
    //         'nim' => 'required|string',
    //     ]);

    //     $buku_pinjam = $request->input('buku_pinjam');
    //     $nim = $request->input('nim');

    //     // Menyusun ulang array $buku_pinjam sehingga 'kode_buku' muncul sebelum 'kode_pinjam'
    //     $formatted_buku_pinjam = [];
    //     foreach ($buku_pinjam as $buku) {
    //         $formatted_buku_pinjam[] = [
    //             'kode_buku' => $buku['kode_buku'],
    //             'kode_pinjam' => 'P' . time(),
    //         ];
    //     }

    //     $peminjaman = Peminjaman::create([
    //         'kode_pinjam' => 'P' . time(),
    //         'nim' => $nim,
    //         'tgl_pinjam' => now(),
    //         'tgl_kembali' => date('Y-m-d', strtotime('+7 days')),
    //         'status' => 'dipinjam',
    //     ]);

    //     // Menggunakan $formatted_buku_pinjam yang sudah disusun ulang
    //     $peminjaman->buku()->attach($formatted_buku_pinjam);

    //     $qrCodeUrl = QrCode::size(300)
    //         ->format('png')
    //         ->generate($peminjaman->kode_pinjam);

    //     // $qrCodePath = 'qr_code/' . $peminjaman->kode_pinjam . '.png';
    //     // Generate QR code
    //     $qrCode = QrCode::format('png')->size(300)->generate($peminjaman->kode_pinjam);
    //     // Path untuk menyimpan gambar QR code
    //     $qrCodePath = 'qr_code/' . $peminjaman->kode_pinjam . '.png';

    //     // Simpan QR code sebagai file sementara
    //     file_put_contents(public_path($qrCodePath), $qrCode);

    //     // Buka gambar QR code dengan Intervention Image
    //     $image = Image::make(public_path($qrCodePath));

    //     // Tambahkan border
    //     $image->border(10, 'blue');

    //     // Simpan gambar dengan border
    //     $image->save();

    //     $data_email = [
    //         'subject' => 'SMART LIBRARY',
    //         'sender_name' => 'azzizdev2@gmail.com',
    //         'receiver_email' => 'aminnurazziz1@gmail.com',
    //         'isi_email' => 'Peminjaman berhasil, silahkan tunjukkan QR Code ini kepada petugas perpustakaan untuk pengembalian. Terima kasih.',
    //         'qr_code' => $qrCodeUrl,
    //         'kode_pinjam' => $peminjaman->kode_pinjam,
    //         'tgl_pinjam' => $peminjaman->tgl_pinjam,
    //         'tgl_kembali' => $peminjaman->tgl_kembali,
    //         'status' => $peminjaman->status,
    //     ];
    //     // Mengirim email
    //     // Mail::send(new KirimEmail($data_email));
    //     Mail::send(new KirimEmail($data_email, $qrCodePath));

    //     return view('success_pinjam', $data_email);
    // }
