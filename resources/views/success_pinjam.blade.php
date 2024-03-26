<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Struk Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 120mm; /* Ukuran kertas struk */
        }
        .container {
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #000;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 5px;
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            padding: 3px;
            text-align: left;
            border-bottom: 1px dashed #000;
        }
        th {
            font-weight: bold;
        }
        .qr-code {
            text-align: center;
            margin-top: 10px;
        }
        .qr-code img {
            max-width: 100%;
            height: auto;
        }
        .print-button {
            text-align: center;
            margin-top: 10px;
        }
        button {
            padding: 5px 15px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .checkbox-container {
            text-align: center;
            margin-top: 20px;
        }
        .checkbox-container label {
            margin-right: 20px;
        }
        .email-form {
            display: none;
            margin-top: 20px;
        }
        .email-form input[type="email"] {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Struk Peminjaman Buku</h1>
        <p><strong>Data Peminjaman:</strong></p>
        <table>
            <tr>
                <th>Kode Peminjaman</th>
                <td>{{ $data_email['data_peminjaman']->kode_pinjam }}</td>
            </tr>
            <tr>
                <th>Tanggal Pinjam</th>
                <td>{{ $data_email['data_peminjaman']->tgl_pinjam }}</td>
            </tr>
            <tr>
                <th>Tanggal Kembali</th>
                <td>{{ $data_email['data_peminjaman']->tgl_kembali }}</td>
            </tr>
        </table>
        <p><strong>Detail Buku yang Dipinjam:</strong></p>
        <table>
            <tr>
                <th>No</th>
                <th>Judul Buku</th>
                <th>Kode Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
            </tr>
            @foreach($data_email['buku_dipinjam'] as $buku)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $buku->judul_buku }}</td>
                    <td>{{ $buku->kode_buku }}</td>
                    <td>{{ $data_email['data_peminjaman']->tgl_pinjam }}</td>
                    <td>{{ $data_email['data_peminjaman']->tgl_kembali }}</td>
                </tr>
            @endforeach
        </table>
        <div class="qr-code">
            @foreach ($qrCodePathArray as $index => $item)
            <p><strong>QR Code Buku {{ $index + 1 }}</strong></p>
            <img src="{{ $item }}" alt="QR Code Buku {{ $index + 1 }}">
            @endforeach
        </div>
        <div class="checkbox-container">
            <label for="print-struk">Cetak Struk</label>
            <input type="checkbox" id="print-struk">
            <label for="input-email">Isi Alamat Email</label>
            <input type="checkbox" id="input-email" onchange="toggleEmailForm()">
        </div>
        <div class="email-form" id="email-form">
            <form>
                <label for="email">Alamat Email:</label>
                <input type="email" id="email" name="email">
                <input type="hidden" name="kode_pinjam" id="kode_pinjam" value="{{ $data_email['data_peminjaman']->kode_pinjam }}">
            </form>
        </div>
        <div class="print-button">
            <button onclick="submitAction()">Selesai</button>
        </div>
    </div>

    <script>
        function submitAction() {
            var printStruk = document.getElementById("print-struk").checked;
            var inputEmail = document.getElementById("input-email").checked;  

            if (printStruk) {
                window.print();
            }
            if (inputEmail) {
                var email = document.getElementById("email").value;
                var kode_pinjam = document.getElementById("kode_pinjam").value;
                // console.log(email);
                sendDataToEndpoint(email, kode_pinjam );
            }
        }
        function sendDataToEndpoint(email, kode_pinjam) {
            var url = "http://127.0.0.1:8000/sendStruk"; // Ganti dengan URL endpoint yang sesuai
            var data = { email: email , kode_pinjam: kode_pinjam};
            console.log(data);

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (response.ok) {
                    console.log("Data terkirim");
                } else {
                    console.error("Gagal mengirim data");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }
        function toggleEmailForm() {
            var emailForm = document.getElementById("email-form");
            var inputEmail = document.getElementById("input-email");

            if (inputEmail.checked) {
                emailForm.style.display = "block";
            } else {
                emailForm.style.display = "none";
            }
        }
    </script>
</body>
</html>
