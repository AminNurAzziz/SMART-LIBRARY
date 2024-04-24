import './style.css';

import QrScanner from 'qr-scanner';
import QrFrame from './QrFrame.svg';
import { useEffect, useRef, useState } from 'react';
import _ from 'lodash';
import axios from '@/utils/axios';
import { useStudentAuthApiState, useStudentStore } from '@/providers/auth.provider';
import { useNavigate } from 'react-router';
import { PATH_DASHBOARD } from '@/routes/paths';
import { StudentAuthResponseData } from '@/@types/auth/auth-types';

function QrReader({ startScan }: Readonly<{ startScan: boolean }>) {
  const scanner = useRef<QrScanner>();
  const videoElement = useRef<HTMLVideoElement>(null);
  const qrBoxElement = useRef<HTMLDivElement>(null);
  const navigate = useNavigate();

  const [qrOn, setQrOn] = useState<boolean>(false);
  const [scannedResult, setScannedResult] = useState<string | undefined>('');

  const { login } = useStudentStore();
  const { setLoading, setError } = useStudentAuthApiState();

  const getStudentData = async (data: string) => {
    try {
      setLoading(true);
      const response: StudentAuthResponseData = await axios.get('student', {
        params: { nim: data },
      });
      const responseData = {
        profile: response.data?.student,
        borrowedData: response?.data?.borrowed_data,
      };
      login(responseData);
      navigate(PATH_DASHBOARD.student);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      setError(true, error.message);
    }
  };

  const getKodePeminjaman = async (data: string) => {
    try {
      console.log(data);
      setLoading(true);
      const hardcodedId = 'KD-P5805155635ZED';
      const response = await axios.get(`peminjaman-buku/${hardcodedId}`);
      console.log(response);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      setError(true, error.message);
    }
  };

  const handleScanData = _.debounce(async (data: string) => {
    if (/^\d/.test(data)) {
      return await getStudentData(data);
    }

    if (data.startsWith('KD')) {
      return await getKodePeminjaman(data);
    }

    console.log('Data is not a number');
  }, 500);

  // Success
  const onScanSuccess = (result: QrScanner.ScanResult) => {
    console.log(result);
    setScannedResult(result?.data);
    handleScanData(result?.data);
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
          Qr Scanned Please Wait...
        </p>
      )}
    </div>
  );
}

export default QrReader;
