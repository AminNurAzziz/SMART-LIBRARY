import { AppBar, Box, Toolbar, Typography } from '@mui/material';
import Image from 'mui-image';
import { Outlet } from 'react-router';
import Main from '../service/Main';
import NavVertical from '../service/nav/NavVertical';
import { useSettingsContext } from '@/components/settings';
import { useState } from 'react';
import { Link } from 'react-router-dom';
import { PATH_STUDENT } from '@/routes/paths';

const NavbarStudent = () => {
  return (
    <AppBar position="static" color="inherit">
      <Toolbar sx={{ display: 'flex', gap: 2, justifyContent: 'space-between' }}>
        <Box component={Link} to={PATH_STUDENT.root} sx={{ display: 'flex', gap: 2 }}>
          <Image src="/logo/logo_polinema.png" width={50} height={50} />
          <Image src="/logo/logo_library.png" width={100} height={50} />
        </Box>

        <Box sx={{ display: 'flex', gap: 2 }}>
          <Typography variant="body1">Book Loans</Typography>
          <Typography variant="body1">Book Reservations</Typography>
          <Typography variant="body1">History</Typography>
          <Typography variant="body1">Logout</Typography>
        </Box>
      </Toolbar>
    </AppBar>
  );
};

function StudentLayout() {
  const { themeLayout } = useSettingsContext();

  const [open, setOpen] = useState(false);

  const isNavHorizontal = themeLayout === 'horizontal';

  const isNavMini = themeLayout === 'mini';

  const handleClose = () => {
    setOpen(false);
  };

  const renderNavVertical = <NavVertical openNav={open} onCloseNav={handleClose} />;

  if (isNavHorizontal) {
    return (
      <>
        <Main>
          <Outlet />
        </Main>
      </>
    );
  }

  if (isNavMini) {
    return (
      <>
        <NavbarStudent />
        <Box
          sx={{
            display: { lg: 'flex' },
            minHeight: { lg: 1 },
          }}
        >
          <Main>
            <Outlet />
          </Main>
        </Box>
      </>
    );
  }

  return (
    <>
      <Box
        sx={{
          display: { lg: 'flex' },
          minHeight: { lg: 1 },
        }}
      >
        {renderNavVertical}
        <Main>
          <Outlet />
        </Main>
      </Box>
    </>
  );
}

export default StudentLayout;
