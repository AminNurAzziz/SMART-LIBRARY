import { AppBar, Toolbar } from '@mui/material';
import Image from 'mui-image';
import { Outlet } from 'react-router';
import Main from '../service/Main';

function GuestLayout() {
  return (
    <>
      <AppBar position="static" color="inherit">
        <Toolbar sx={{ display: 'flex', gap: 2, justifyContent: 'center' }}>
          <Image src="/logo/logo_polinema.png" width={50} height={50} />
          <Image src="/logo/logo_library.png" width={100} height={50} />
        </Toolbar>
      </AppBar>
      <Main>
        <Outlet />
      </Main>
    </>
  );
}

export default GuestLayout;
