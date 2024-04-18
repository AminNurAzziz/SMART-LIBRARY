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

        <div id="alertModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Peringatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    Maaf, Anda hanya dapat meminjam maksimum 2 buku.
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
    <div></div>

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
            <div id="daftarBuku" class="col-md-6">
                <div id="" class="col-md-12">
                    <!-- Tampilkan data buku yang dipinjam di sini -->
                    @if($data_peminjaman->count() > 0)
        
                        <h2 class="mb-4">Buku yang Sedang Dipinjam</h2>
                        <div class="table-responsive mx-auto"> 
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Judul Buku</th>
                                        <th scope="col">Tanggal Pinjam</th>
                                        <th scope="col">Tanggal Kembali</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data_peminjaman as $peminjaman)
                                    <tr>
                                        <td>{{ $peminjaman->judul_buku }}</td>
                                        <td>{{ $peminjaman->tgl_pinjam }}</td>
                                        <td id="tglKembali{{ $peminjaman->kode_buku }}">{{ $peminjaman->tgl_kembali }}</td>
                                        <td id="status{{ $peminjaman->kode_buku }}">{{ $peminjaman->status }}</td>
                                        {{-- <td>
                                            <button class="btn btn-primary btn-sm perpanjangBtnBuku" data-id="{{ $peminjaman->kode_buku }}" data-peminjaman-id="{{ $peminjaman->kode_buku }}">Perpanjang</button>
                                            <button class="btn btn-danger btn-sm kembalikan" data-id="{{ $peminjaman->id_peminjaman }}">Kembalikan</button>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                
                                </tbody>
                            </table>

                    </div>
                    @else
                        <p>Tidak ada buku yang dipinjam.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div id="daftarBukuDipinjam" class="mt-5 col-md-6"></div>
            
        </div>

        
        <div class="row justify-content-center"> <!-- Pindahkan class ini ke sini -->
            <form id="pinjamForm" action="/pinjam" method="POST" class="col-md-6">
                @csrf
                <input type="hidden" name="nim" value="{{ $student->nim }}">
                {{-- @if($data_peminjaman->count() > 0)
                    @foreach($data_peminjaman as $i => $peminjaman)
                        <input type="hidden" name="buku_pinjam[{{ $i }}][kode_buku]" value="{{ $peminjaman->kode_buku }}">
                        <input type="hidden" name="buku_pinjam[{{ $i }}][tanggal_pinjam]" value="{{ $peminjaman->tgl_pinjam }}">
                        <input type="hidden" name="buku_pinjam[{{ $i }}][tanggal_kembali]" value="{{ $peminjaman->tgl_kembali }}">
                    @endforeach
                @endif --}}
            
                <input type="submit" value="Pinjam" class="btn btn-success btn-lg btn-block mt-3">
            </form>
        </div>

    </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const dataPeminjaman = {!! json_encode($data_peminjaman) !!};
        console.log(dataPeminjaman);
    </script>
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>

    