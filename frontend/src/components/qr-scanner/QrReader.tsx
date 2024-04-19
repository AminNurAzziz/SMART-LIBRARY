import './style.css';

import QrScanner from 'qr-scanner';
import QrFrame from './QrFrame.svg';
import { useEffect, useRef, useState } from 'react';

function QrReader({ startScan }: { startScan: boolean }) {
  const scanner = useRef<QrScanner>();
  const videoElement = useRef<HTMLVideoElement>(null);
  const qrBoxElement = useRef<HTMLDivElement>(null);

  const [qrOn, setQrOn] = useState<boolean>(false);
  const [scannedResult, setScannedResult] = useState<string | undefined>('');

  // Success
  const onScanSuccess = (result: QrScanner.ScanResult) => {
    console.log(result);
    setScannedResult(result?.data);
  };

  // Fail
  const onScanFail = (err: string | Error) => {
    console.log(err);
  };

  const startScanner = () => {
    if (videoElement.current && !scanner.current) {
      scanner.current = new QrScanner(videoElement.current, onScanSuccess, {
        onDecodeError: onScanFail,
        preferredCamera: 'environment',
        highlightScanRegion: true,
        highlightCodeOutline: true,
        overlay: qrBoxElement.current || undefined,
      });

      scanner.current
        .start()
        .then(() => setQrOn(true))
        .catch((err) => {
          if (err) setQrOn(false);
        });
    }
  };

  useEffect(() => {
    if (startScan) {
      startScanner();
    }

    return () => {
      if (!videoElement?.current) {
        scanner?.current?.stop();
      }
    };
  }, [startScan]);

  return (
    <div className="qr-reader">
      <video ref={videoElement}></video>
      <div ref={qrBoxElement} className="qr-box">
        <img src={QrFrame} width={256} height={256} className="qr-frame" />
      </div>

      {scannedResult && (
        <p
          style={{
            position: 'absolute',
            top: 0,
            left: 0,
            zIndex: 99999,
            color: 'white',
          }}
        >
          Scanned Result: {scannedResult}
        </p>
      )}
    </div>
  );
}

export default QrReader;
