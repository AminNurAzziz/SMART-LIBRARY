// import './bootstrap';
document.getElementById('searchForm').addEventListener('submit', function (event) {
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
document.getElementsByClassName("close")[0].onclick = function () {
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
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Buku ${index + 1}</h5>
                <ul class="list-unstyled">
                    <li><strong>Kode Buku:</strong> ${buku.kode_buku}</li>
                    <li><strong>ISBN:</strong> ${buku.isbn}</li>
                    <li><strong>Judul Buku:</strong> ${buku.judul_buku}</li>
                    <li><strong>Penerbit:</strong> ${buku.penerbit}</li>
                    <li><strong>Stok:</strong> ${buku.stok}</li>
                    <li><strong>Tanggal Pinjam:</strong> ${buku.tanggal_pinjam}</li>
                    <li><strong>Tanggal Kembali:</strong> ${buku.tanggal_kembali}</li>
                </ul>
                <button type="button" class="btn btn-danger mt-3" onclick="hapusBuku(${index})">Hapus</button>
                <button type="button" class="btn btn-primary mt-3 perpanjangBtn" data-index="${index}">Perpanjang</button>
            </div>
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
document.getElementById('pinjamBtn').addEventListener('click', async function () {
    if (document.getElementById('kodeBuku').textContent) {
        // Ambil data buku dari modal
        const bukuData = {
            kode_buku: document.getElementById('kodeBuku').textContent,
            isbn: document.getElementById('isbn').textContent,
            judul_buku: document.getElementById('judulBuku').textContent,
            penerbit: document.getElementById('penerbit').textContent,
            stok: document.getElementById('stok').textContent
        }

        // Ambil data regulasi dari API
        const regulation = await fetchRegulation();
        if (!regulation) {
            console.error('Failed to get regulation data');
            return;
        }

        // Set tanggal pinjam
        const tanggalPinjam = new Date().toISOString().slice(0, 10);
        // Set tanggal kembali 7 hari dari tanggal pinjam
        const tanggalKembali = new Date();
        tanggalKembali.setDate(tanggalKembali.getDate() + regulation.max_loan_days);
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


// Fungsi untuk menghapus buku dari daftar buku yang dipinjam
function hapusBuku(index) {
    // Hapus buku dari array bukuDipinjam
    bukuDipinjam.splice(index, 1);

    // Perbarui tampilan daftar buku yang dipinjam
    renderDaftarBukuDipinjam();
}

// Fungsi untuk menangani tindakan perpanjang peminjaman
async function perpanjangPinjam(index) {
    // Tampilkan modal konfirmasi
    $('#confirmPerpanjanganModal').modal('show');
    // Tambahkan event listener untuk tombol "Perpanjang" di modal konfirmasi
    document.getElementById('confirmPerpanjanganBtn').addEventListener('click', async function () {
        // Ambil data regulasi dari API
        const regulation = await fetchRegulation();
        if (!regulation) {
            console.error('Failed to get regulation data');
            return;
        }
        // Ambil tanggal kembali dari buku yang dipinjam
        const tanggalKembali = new Date(bukuDipinjam[index].tanggal_kembali);

        // Tambahkan 7 hari pada tanggal kembali
        tanggalKembali.setDate(tanggalKembali.getDate() + regulation.max_loan_days);

        // Ubah format tanggal kembali ke format ISO string
        const tanggalKembaliFormatted = tanggalKembali.toISOString().slice(0, 10);

        // Update tanggal kembali pada buku yang dipinjam
        bukuDipinjam[index].tanggal_kembali = tanggalKembaliFormatted;

        // Perbarui tampilan daftar buku yang dipinjam
        renderDaftarBukuDipinjam();


        // Nonaktifkan tombol setelah diklik sekali
        const button = document.querySelector(`[data-index="${index}"].perpanjangBtn`);
        button.disabled = true;

        // Sembunyikan modal konfirmasi
        $('#confirmPerpanjanganModal').modal('hide');

        // Tampilkan pesan flash
        const flashMessage = document.getElementById('flashMessage');
        flashMessage.innerText = 'Peminjaman berhasil diperpanjang.';
        flashMessage.classList.add('alert', 'alert-success', 'mt-3');
        flashMessage.style.display = 'block';
        setTimeout(function () {
            flashMessage.style.display = 'none';
        }, 3000); // Sembunyikan pesan setelah 3 detik
    });
}



// Event delegation untuk menangani klik tombol "Perpanjang"
document.addEventListener('click', function (event) {
    if (event.target.classList.contains('perpanjangBtn')) {
        const index = event.target.getAttribute('data-index');
        perpanjangPinjam(index);
    }
});




// Fungsi untuk mengambil data regulasi dari API
async function fetchRegulation() {
    try {
        const response = await fetch('http://127.0.0.1:8000/getRegulation');
        if (!response.ok) {
            throw new Error('Failed to fetch regulation data');
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching regulation data:', error);
        return null;
    }
}


$(document).ready(function () {
    // Ketika form pinjam disubmit
    $('#pinjamForm').submit(function () {
        // Tampilkan loading
        $('#loading').show();
    });
});