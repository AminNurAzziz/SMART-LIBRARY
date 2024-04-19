import { AppBar, Box, Toolbar } from '@mui/material';
import Image from 'mui-image';
// import Polinema from '../../assets'
import { useTheme } from '@mui/material/styles';
import { Outlet } from 'react-router';

function GuestLayout() {
  const theme = useTheme();
  return (
    <>
      <AppBar position="static" color='inherit'>
        <Toolbar sx={{ display: 'flex', gap: 2, justifyContent: 'center' }}>
          <Image src="/logo/logo_polinema.png" width={50} height={50} />
          <Image src="/logo/logo_library.png" width={100} height={50} />
        </Toolbar>
      </AppBar>
      <Box mt={4}>
        <Outlet />
      </Box>
    </>
  );
}

export default GuestLayout;
