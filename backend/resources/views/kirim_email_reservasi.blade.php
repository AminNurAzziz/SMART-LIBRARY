<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Peminjaman</title>
    <style>
        /* Styling untuk QR Code */

        /* Container styling */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Heading styling */
        h1, h2, h3, h4, h5, h6 {
            color: #333;
        }

        /* Paragraph styling */
        p {
            color: #666;
        }

        /* Divider styling */
        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        /* Button styling */
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        /* Responsiveness */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 style="color: green;">{{ $data_email['email_content'] }}</h3>
        <div class="divider"></div>
        <p><strong>Kode Peminjaman:</strong> {{ $data_email['reservation_data']['kode_reservasi'] }}</p>
        <p><strong>Tanggal Pinjam:</strong> {{ $data_email['reservation_data']['tanggal_reservasi'] }}</p>
        <p><strong>Tanggal Kembali:</strong> {{ $data_email['reservation_data']['tanggal_ambil'] }}</p>
        <p><strong>Status:</strong> {{ $data_email['reservation_data']['status'] }}</p>
        
        
        {{-- Menampilkan detail buku yang dipinjam --}}
        <h2>Detail Buku yang Dipinjam:</h2>
        <div>
            <p><strong>Judul:</strong> {{ $data_email['book_detail']->judul_buku }}</p>
            <p><strong>Kode Buku:</strong> {{ $data_email['book_detail']->kode_buku }}</p>
            <p><strong>ISBN:</strong> {{ $data_email['book_detail']->isbn }}</p>
            <p><strong>Penerbit:</strong> {{ $data_email['book_detail']->penerbit }}</p>
        </div>
        

        <div class="divider"></div>
        <h2>Peminjam:</h2>
        <p><strong>NIM:</strong> {{ $data_email['student']->nim }}</p>
        <p><strong>Nama:</strong> {{ $data_email['student']->nama_mhs }}</p>
        <p><strong>Email:</strong> {{ $data_email['student']->email_mhs }}</p>

        {{-- Menampilkan QR code --}}
        {{-- @if(isset($data_email['qr_code_path']))
            <div class="qr-code">
                <img src="{{ $message->embed($data_email['qr_code_path']) }}" alt="QR Code">
            </div>
        @endif --}}

        {{-- Menambahkan QR code sebagai lampiran --}}
        @if(isset($data_email['qr_code_path']))
            <?php
                $qrCodePath = public_path($data_email['qr_code_path']);
            ?>
        @endif
    </div>
</body>
</html>
