<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <!-- Tambahkan link CSS untuk Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div id="loading" class="text-center mb-3" style="display: none ;">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Model untuk konfirmasi perpanjangan -->
        <div id="confirmPerpanjanganModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Perpanjangan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin memperpanjang peminjaman buku ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="confirmPerpanjanganBtn" class="btn btn-primary">Perpanjang</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="header">
            <h1 class="text-center mb-5">Informasi Mahasiswa</h1>
        </div>
        
        <div class="student-info">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @if($student)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Detail Mahasiswa</h5>
                            <ul class="list-unstyled">
                                <li><strong>NIM:</strong> {{ $student->nim }}</li>
                                <li><strong>Nama:</strong> {{ $student->nama_mhs }}</li>
                                <li><strong>Program Studi:</strong> {{ $student->prodi_mhs }}</li>
                                <li><strong>Kelas:</strong> {{ $student->kelas_mhs }}</li>
                                <li><strong>Email:</strong> {{ $student->email_mhs }}</li>
                                <li><strong>Status:</strong> {{ $student->status_mhs }}</li>
                            </ul>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-danger" role="alert">
                        Mahasiswa tidak ditemukan.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="book-search">
            <h2 class="text-center mb-4">Pencarian Buku</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form id="searchForm" class="mb-4">
                        <div class="form-group">
                            <label for="kode">Masukkan Kode Buku:</label>
                            <input type="text" class="form-control" id="kode" name="kode" value="{{ request('kode') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal-container">
            <!-- Modal untuk menampilkan informasi buku -->
            <div id="myModal" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Informasi Buku</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body" id="bukuInfo"></div>
                        <div class="modal-footer">
                            <button id="pinjamBtn" type="button" class="btn btn-primary">Pinjam</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div id="flashMessage" class="alert alert-success mt-3 mt-5 col-md-6 text-center" style="display: none;"></div>
        </div>  
        <div class="row justify-content-center">
            <div id="daftarBukuDipinjam" class="mt-5 col-md-6"></div>
        </div>
        <div class="row justify-content-center"> <!-- Pindahkan class ini ke sini -->
            <form id="pinjamForm" action="/pinjam" method="POST" class="col-md-6">
                @csrf
                <input type="hidden" name="nim" value="{{ $student->nim }}">
                <!-- Elemen buku dipinjam akan ditambahkan di sini -->
                <input type="submit" value="Pinjam" class="btn btn-success btn-lg btn-block mt-3">
            </form>
        </div>

    </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>



{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <!-- Tambahkan link CSS untuk Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang */
        }
        .container {
            margin-top: 50px;
        }
        .modal-content {
            background-color: #f8f9fa; /* Warna latar belakang modal */
        }
        .modal-body {
            color: #000; /* Warna teks modal */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4 text-center">Informasi Mahasiswa</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if($student)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Detail Mahasiswa</h5>
                        <p class="card-text"><strong>NIM:</strong> {{ $student->nim }}</p>
                        <p class="card-text"><strong>Nama:</strong> {{ $student->nama_mhs }}</p>
                        <p class="card-text"><strong>Program Studi:</strong> {{ $student->prodi_mhs }}</p>
                        <p class="card-text"><strong>Kelas:</strong> {{ $student->kelas_mhs }}</p>
                        <p class="card-text"><strong>Email:</strong> {{ $student->email_mhs }}</p>
                        <p class="card-text"><strong>Status:</strong> {{ $student->status_mhs }}</p>
                    </div>
                </div>
                @else
                <div class="alert alert-danger" role="alert">
                    Mahasiswa tidak ditemukan.
                </div>
                @endif
            </div>
        </div>

        <h1 class="mt-5 mb-4 text-center">Pencarian Buku</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="searchForm" class="mb-4">
                    <div class="form-group">
                        <label for="kode">Masukkan Kode Buku:</label>
                        <input type="text" class="form-control" id="kode" name="kode" value="{{ request('kode') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>
        </div>

        <!-- Modal untuk menampilkan informasi buku -->
        <div id="myModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Informasi Buku</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" id="bukuInfo"></div>
                    <div class="modal-footer">
                        <button id="pinjamBtn" type="button" class="btn btn-primary">Pinjam</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="daftarBukuDipinjam" class="mt-5"></div>
        <form id="pinjamForm" action="/pinjam" method="POST">
            @csrf
            <input type="hidden" name="nim" value="{{ $student->nim }}">
            <!-- Elemen buku dipinjam akan ditambahkan di sini -->
            <input type="submit" value="Pinjam">
        </form>
    </div>
    

    <!-- Tambahkan script untuk Bootstrap dan JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Menghentikan perilaku default dari form
            const kodeBuku = document.getElementById('kode').value;

            // Kirim permintaan AJAX
            fetch(`/getBuku?kode=${kodeBuku}`)
                .then(response => response.json())
                .then(data => {
                    // Tampilkan data buku di dalam modal jika ditemukan
                    const modal = document.getElementById('myModal');
                    const bukuInfo = document.getElementById('bukuInfo');
                    if (data) {
                        bukuInfo.innerHTML = `
                        <h2>Informasi Buku</h2>
                        <p><strong>Kode Buku:</strong> <span id="kodeBuku">${data.kode_buku}</span></p>
                        <p><strong>ISBN:</strong> <span id="isbn">${data.isbn}</span></p>
                        <p><strong>Judul Buku:</strong> <span id="judulBuku">${data.judul_buku}</span></p>
                        <p><strong>Penerbit:</strong> <span id="penerbit">${data.penerbit}</span></p>
                        <p><strong>Stok:</strong> <span id="stok">${data.stok}</span></p>
                        `;
                        modal.style.display = "block"; // Tampilkan modal
                    } else {
                        bukuInfo.innerHTML = '<p>Buku tidak ditemukan.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Fungsi untuk menutup modal ketika tombol close diklik
        document.getElementsByClassName("close")[0].onclick = function() {
            document.getElementById('myModal').style.display = "none";
        }

        let bukuDipinjam = [];
        let index = 1;
        function renderDaftarBukuDipinjam() {
            const daftarBukuDipinjam = document.getElementById('daftarBukuDipinjam');
            const form = document.getElementById('pinjamForm');



            daftarBukuDipinjam.innerHTML = '<h2 class="mb-4 text-center">Daftar Buku yang Dipinjam</h2>';
            bukuDipinjam.forEach((buku, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `buku_pinjam[${index}][kode_buku]`;
                input.value = buku.kode_buku;
                form.appendChild(input);


                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = `buku_pinjam[${index}][tanggal_pinjam]`;
                input2.value = buku.tanggal_pinjam;

                form.appendChild(input2);

                const input3 = document.createElement('input');
                input3.type = 'hidden';
                input3.name = `buku_pinjam[${index}][tanggal_kembali]`;
                input3.value = buku.tanggal_kembali;

                form.appendChild(input3);


                const bukuItem = document.createElement('div');
                bukuItem.classList.add('card', 'mb-3');
                bukuItem.innerHTML = `
                    <div class="card-body">
                        <h5 class="card-title mb-3">Buku ${index + 1}</h5>
                        <p class="card-text"><strong>Kode Buku:</strong> ${buku.kode_buku}</p>
                        <p class="card-text"><strong>ISBN:</strong> ${buku.isbn}</p>
                        <p class="card-text"><strong>Judul Buku:</strong> ${buku.judul_buku}</p>
                        <p class="card-text"><strong>Penerbit:</strong> ${buku.penerbit}</p>
                        <p class="card-text"><strong>Stok:</strong> ${buku.stok}</p>
                        <p class="card-text"><strong>Tanggal Pinjam:</strong> ${buku.tanggal_pinjam}</p>
                        <p class="card-text"><strong>Tanggal Kembali:</strong> ${buku.tanggal_kembali}</p>
                    </div>
                `;
                daftarBukuDipinjam.appendChild(bukuItem);
            });
        }

        // Fungsi untuk menambah buku ke daftar buku yang dipinjam
        function tambahBukuDipinjam(buku) {
            bukuDipinjam.push(buku);
            renderDaftarBukuDipinjam();
        }

        // Fungsi untuk menangani tindakan pinjam
        document.getElementById('pinjamBtn').addEventListener('click', function() {
            if (document.getElementById('kodeBuku').textContent) {
                // Ambil data buku dari modal
                const bukuData = {
                    kode_buku: document.getElementById('kodeBuku').textContent,
                    isbn: document.getElementById('isbn').textContent,
                    judul_buku: document.getElementById('judulBuku').textContent,
                    penerbit: document.getElementById('penerbit').textContent,
                    stok: document.getElementById('stok').textContent
                }
                
                // Set tanggal pinjam
                const tanggalPinjam = new Date().toISOString().slice(0, 10);
                // Set tanggal kembali 7 hari dari tanggal pinjam
                const tanggalKembali = new Date();
                tanggalKembali.setDate(tanggalKembali.getDate() + 7);
                const tanggalKembaliFormatted = tanggalKembali.toISOString().slice(0, 10);

                // Simpan tanggal pinjam dan tanggal kembali di dalam bukuData
                bukuData.tanggal_pinjam = tanggalPinjam;
                bukuData.tanggal_kembali = tanggalKembaliFormatted;
                
                // Tambahkan data buku ke daftar buku yang dipinjam
                tambahBukuDipinjam(bukuData);
                // Sembunyikan modal setelah melakukan pinjam
                document.getElementById('myModal').style.display = "none";
            }
        });
    </script>
</body>
</html> --}}
