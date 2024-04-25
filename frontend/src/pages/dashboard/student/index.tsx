import {
  Box,
  Button,
  Container,
  Dialog,
  DialogContent,
  Grid,
  InputAdornment,
  TextField,
  Typography,
} from '@mui/material';
import { Helmet } from 'react-helmet-async';
import { SeoIllustration } from '@/assets/illustrations';
import { WelcomeBanner, ProfileDetail, BorrowedBookTable } from '@/sections/dashboard/student';
import { ChangeEvent, useEffect, useState } from 'react';
import Iconify from '@/components/iconify';
import axios from '@/utils/axios';
import Label from '@/components/label';
import debounce from 'lodash/debounce';
import { Link } from 'react-router-dom';
import { useStudentStore } from '@/providers/auth.provider';

function StudentDashboard() {
  const [openSearchBar, setopenSearchBar] = useState<boolean>(false);
  const [books, setBooks] = useState<[]>([]);
  const [searchValue, setSearchValue] = useState('');

  const handleOpenSearchBar = (): void => setopenSearchBar(true);
  const { user } = useStudentStore();

  const makeAPICall = async (value: string) => {
    try {
      const response = await axios.get('/products/search?q=' + value);
      debouncedSetBooks(response.data.products, value);
    } catch (error) {
      console.log(error);
    }
  };

  const debouncedAPICall = debounce((value: string) => {
    makeAPICall(value);
  }, 750);

  const debouncedSetBooks = debounce((products: [], value: string) => {
    setBooks(products);
    setSearchValue(value);
  }, 500);

  useEffect(() => {
    return () => {
      debouncedAPICall.cancel();
      debouncedSetBooks.cancel();
    };
  }, []);

  const handleChangeSearch = (e: ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    debouncedAPICall(value);
  };

  const SearchDialog = () => {
    return (
      <Dialog fullWidth open={openSearchBar} onClose={() => setopenSearchBar(false)}>
        <DialogContent sx={{ padding: '35px' }}>
          <Box>
            <TextField
              autoFocus
              onChange={handleChangeSearch}
              margin="dense"
              InputProps={{
                startAdornment: (
                  <InputAdornment position="start">
                    <Iconify icon="eva:search-fill" sx={{ color: 'text.disabled' }} />
                  </InputAdornment>
                ),
                endAdornment: (
                  <InputAdornment position="end">
                    <Label sx={{ opacity: '50%' }} variant="soft">
                      Esc
                    </Label>
                  </InputAdornment>
                ),
              }}
              label="Book Title"
              fullWidth
              variant="standard"
              sx={{ flex: 1 }}
            />
          </Box>

          <Box>
            {searchValue !== '' && (
              <Typography color="GrayText" align="center" variant="h6" sx={{ mt: 3 }}>
                {`Search Results For "${searchValue}"`}
              </Typography>
            )}
            {books.length !== 0 && (
              <Box>
                {books.map((book: any) => (
                  <Box key={book.id} mb={1.5}>
                    <Typography
                      component={Link}
                      to={`/student/book/${book.id}`}
                      sx={{ cursor: 'pointer' }}
                      variant="body2"
                      color="CaptionText"
                    >
                      {book.title}
                    </Typography>
                  </Box>
                ))}
              </Box>
            )}
          </Box>
        </DialogContent>
      </Dialog>
    );
  };

  return (
    <>
      <Helmet>
        <title>Student | Dashboard</title>
      </Helmet>

      <Container maxWidth="xl">
        <SearchDialog />

        <Grid container spacing={3}>
          <Grid item xs={12} md={8}>
            <WelcomeBanner
              title={`Welcome back! \n ${user?.profile.student_name}`}
              description="Start by scanning or searching for a book to borrow."
              img={
                <SeoIllustration
                  sx={{
                    p: 3,
                    width: 360,
                    margin: { xs: 'auto', md: 'inherit' },
                  }}
                />
              }
              action={
                <Box sx={{ display: 'flex', gap: 3 }}>
                  <Button variant="contained">Scan Book</Button>
                  <Button onClick={handleOpenSearchBar} variant="outlined">
                    Search Book
                  </Button>
                </Box>
              }
            />
          </Grid>

          <Grid item xs={12} md={4}>
            <Grid container spacing={3}>
              <Grid item xs={12}>
                <ProfileDetail title="Class" subtitle={user?.profile.class} color="info" />
              </Grid>
              <Grid item xs={12}>
                <ProfileDetail title="NIM" subtitle={user?.profile.nim} color="error" />
              </Grid>
              <Grid item xs={12}>
                <ProfileDetail title="Major" subtitle={user?.profile.major} color="success" />
              </Grid>
            </Grid>
          </Grid>
        </Grid>

        <Grid item xs={12} md={4}>
          <Box mt={4}>
            <BorrowedBookTable
              title="Borrowed Book History"
              tableData={user?.borrowedData}
              tableLabels={[
                { id: 'book_code', label: 'Book Code' },
                { id: 'book_title', label: 'Book Title' },
                { id: 'status', label: 'Book Status', align: 'center' },
                { id: 'borrow_date', label: 'Loan Date', align: 'center' },
                { id: 'return_date', label: 'Return Date', align: 'center' },
                { id: 'action', label: 'Action', align: 'center' },
              ]}
            />
          </Box>
        </Grid>
      </Container>
    </>
  );
}

export default StudentDashboard;
