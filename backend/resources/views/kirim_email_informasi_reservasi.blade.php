<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Reservasi</title>
</head>
<body>
    <p>Halo,</p>
    
    <p>Buku "{{ $data_email['buku_dipinjam'] }}" yang Anda reservasi sudah tersedia. Silahkan ambil buku tersebut di perpustakaan. Terima kasih.</p>
    
    <p>Terima kasih,</p>
    <p>{{ $data_email['sender_name'] }}</p>
</body>
</html>
