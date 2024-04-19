import { Helmet } from 'react-helmet-async';
import { Box, Button, Container, Typography } from '@mui/material';
import { useSettingsContext } from '../../components/settings';
import QrReader from '../../components/qr-scanner/QrReader';
import { useState } from 'react';

// ----------------------------------------------------------------------

export default function ScanCard() {
  const { themeStretch } = useSettingsContext();
  const [startScan, setStartScan] = useState<boolean>(false);

  const handleScan = () => {
    setStartScan((prevStartScan) => !prevStartScan);
  };

  return (
    <>
      <Helmet>
        <title> Welcome</title>
      </Helmet>

      <Container maxWidth={themeStretch ? false : 'xl'}>
        <Box textAlign="center">
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
      </Container>
    </>
  );
}
