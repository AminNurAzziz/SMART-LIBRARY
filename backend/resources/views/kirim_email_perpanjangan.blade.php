<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjangan Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
            margin-top: 0;
        }
        h3 {
            color: #555;
            margin-top: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Perpanjangan Peminjaman Berhasil</h2>
        <p>Selamat! Peminjaman Anda berhasil diperpanjang.</p>
        
        <h3>Detail Peminjaman:</h3>
        <ul>
            <li><strong>Kode Peminjaman:</strong> {{ $data_email['data_perpanjangan']->kode_pinjam }}</li>
            <li><strong>NIM:</strong> {{ $data_email['peminjam']->nim }}</li>
            <li><strong>Judul Buku:</strong> {{ $data_email['buku_dipinjam']->judul_buku }}</li>
            <li><strong>Tanggal Pinjam:</strong> {{ $data_email['data_perpanjangan']->tgl_pinjam }}</li>
            <li><strong>Tanggal Kembali Baru:</strong> {{ $data_email['data_perpanjangan']->tgl_kembali }}</li>
        </ul>

        <p>Terima kasih.</p>
    </div>
</body>
</html>
