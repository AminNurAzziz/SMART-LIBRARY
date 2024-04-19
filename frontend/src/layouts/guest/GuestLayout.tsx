import { AppBar, Button, IconButton, Toolbar, Typography } from '@mui/material';
import Main from '../service/Main';
import { Outlet } from 'react-router';

function GuestLayout() {
  return (
    <>
      <AppBar position='static' color='error'>
        <Toolbar>
          <IconButton size="large" edge="start" color="error" aria-label="menu" sx={{ mr: 2 }}>
            {/* <MenuIcon /> */}
          </IconButton>

          <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
            Geeksforgeeks
          </Typography>

          <Button color="error">Logout</Button>
        </Toolbar>
      </AppBar>
      <Main>
        <Outlet />
      </Main>
    </>
  );
}

export default GuestLayout;
