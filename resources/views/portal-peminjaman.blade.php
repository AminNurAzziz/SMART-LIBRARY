<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code Scanner</title>
  <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      /* background-color: #f3f3f3; Warna latar belakang */
      background-color: #cadef9; /* Warna latar belakang */
    }
    .container {
      margin-top: 50px;
    }
    #scanner-container {
      position: relative;
      width: 300px; /* Lebar kontainer pemindai */
      height: 300px; /* Tinggi kontainer pemindai */
      margin: 0 auto;
      background-color: #fff; /* Warna latar belakang kontainer pemindai */
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
    }
    #video {
      width: 100%;
      height: 100%;
      border-radius: 10px; /* Agar video memiliki sudut yang sama dengan kontainer */
    }
    #scan-button {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      font-size: 18px;
      border-radius: 5px;
    }
    #qr-frame {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      border: 1px solid green; /* Warna garis bingkai hijau */
      width: 70%;
      height: 70%;
      box-sizing: border-box;
      pointer-events: none; /* Mencegah bingkai dari mengganggu interaksi dengan video */
      /* display: none; Sembunyikan bingkai hijau secara default */
      border-radius: 5px;
    }
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .loading-spinner {
      border: 16px solid #f3f3f3; /* Warna latar belakang spinner */
      border-top: 16px solid #3498db; /* Warna spinner */
      border-radius: 50%;
      width: 120px;
      height: 120px;
      animation: spin 2s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
<div class="container">
  <h1 class="text-center mb-4">SMART LIBRARY</h1>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div id="scanner-container">
        <video id="video" playsinline></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <div id="qr-frame"></div> <!-- Elemen untuk bingkai garis hijau -->
      </div>
      <button id="scan-button" style="background: #0F2C56; border-color: #0F2C56" class="btn btn-primary d-block mx-auto mt-3" onclick="startScan()">Start QR Code Scan</button>
      <small id="scan-instructions" class="d-block text-center mt-3">Pastikan KODE QR pada KTM Anda berada dalam bingkai hijau</small>
    </div>
  </div>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-spinner"></div>
</div>

<script>
  let code;
  let mediaStream;

  // Fungsi untuk memulai pemindaian QR code
  function startScan() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const scanButton = document.getElementById('scan-button');
    const qrFrame = document.getElementById('qr-frame');


    // Mengatur video sebagai sumber gambar
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(function (stream) {
        mediaStream = stream; // Simpan referensi ke stream media
        video.srcObject = stream;
        video.setAttribute('playsinline', true); // iOS support
        video.play();
        scanButton.style.display = 'none'; // Sembunyikan tombol setelah pemindaian dimulai

        // Mulai pemindaian setiap 100ms
        setInterval(function () {
          context.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
          code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert',
          });
          if (code) {
            // Memperbarui posisi dan ukuran bingkai hijau berdasarkan posisi QR code
            qrFrame.style.top = code.location.top + 'px';
            qrFrame.style.left = code.location.left + 'px';
            qrFrame.style.width = code.location.width + 'px';
            qrFrame.style.height = code.location.height + 'px';
            qrFrame.style.display = 'block'; // Tampilkan bingkai hijau
            // resultElement.innerHTML = '<p>Data QR Code: ' + code.data + '</p>';
            // Tambahkan tindakan lain yang diinginkan ketika QR code terdeteksi di sini
            callStudentEndpoint();
          } else {
            qrFrame.style.display = 'none'; // Sembunyikan bingkai hijau jika tidak ada QR code terdeteksi
          }
        }, 100);
      })
      .catch(function (error) {
        console.error('Error accessing the camera:', error);
      });
  }

  function callStudentEndpoint() {
    console.log("CODE"+code.data);
    const nim = code.data; // Get the nim from the QR code

    const loadingOverlay = document.getElementById('loadingOverlay');
    // Menghentikan track media ketika QR code berhasil dipindai
    if (mediaStream) {
      mediaStream.getTracks().forEach(track => {
        track.stop();
      });
    }
    // Tampilkan loading overlay saat memulai pemindaian
    loadingOverlay.style.display = 'flex';

    // Redirect the user to the students endpoint with nim parameter
    window.location.href = '/students?nim=' + nim;
    console.log("NIM"+nim);
  }
</script>
</body>
</html>
