import { Helmet } from 'react-helmet-async';
import { Box, Button, Skeleton, Typography } from '@mui/material';
import QrReader from '../../components/qr-scanner/QrReader';
import { useState } from 'react';
import { useStudentAuthApiState } from '@/providers/auth.provider';

// ----------------------------------------------------------------------

export default function ScanCard() {
  const [startScan, setStartScan] = useState<boolean>(false);
  const { isLoading } = useStudentAuthApiState();

  const handleScan = () => {
    setStartScan((prevStartScan) => !prevStartScan);
  };

  const generateComponent = () => {
    if (isLoading) {
      return (
        <Box marginLeft={10}>
          <Skeleton
            sx={{ margin: 'auto', borderRadius: '10px' }}
            variant="rectangular"
            width={500}
            height={300}
          />
          <Box marginTop={4} />
          <Skeleton
            sx={{ margin: 'auto', borderRadius: '10px' }}
            variant="rectangular"
            width={200}
            height={50}
          />
        </Box>
      );
    }

    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', marginLeft: '5rem' }}>
        <Box sx={{ margin: 'auto', textAlign: 'center' }}>
          <Typography sx={{ textTransform: 'capitalize', color: 'black' }} variant="h3">
            Please scan your KTM!
          </Typography>
          <Typography mt={3} mb={3} color="GrayText" variant="body1">
            Scan to start borrowing available books
          </Typography>
          {startScan && <QrReader startScan={startScan} />}

          <Button onClick={handleScan} variant="contained" size="large">
            {startScan ? 'Reset' : 'Get Started'}
          </Button>
        </Box>
      </Box>
    );
  };

  return (
    <>
      <Helmet>
        <title> Welcome</title>
      </Helmet>
      {generateComponent()}
    </>
  );
}
