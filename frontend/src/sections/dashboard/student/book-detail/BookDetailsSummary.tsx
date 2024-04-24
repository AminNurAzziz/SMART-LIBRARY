// @mui
import { Stack, Button, Divider, Typography, Snackbar, Alert } from '@mui/material';
// @types
import { IProduct } from '../../../../@types/product';
import { useBookCartStore } from '@/providers/cart.provider';
import { useEffect, useState } from 'react';
// _mock
// ----------------------------------------------------------------------

type Props = {
  product: IProduct;
};

export default function BookDetailSummary({ product, ...other }: Props) {
  const { addToCart, totalCart, maxCart } = useBookCartStore();
  const [snackbarCart, setSnackbarCart] = useState<boolean>(false);
  const { name, available, category } = product;

  const details = [
    { label: 'Book Code', value: 'MNO345' },
    { label: 'Category', value: category },
    { label: 'Author', value: name },
    { label: 'Publisher', value: 'ABC Publisher' },
    { label: 'Published Date', value: '12 Dec 2021' },
    { label: 'Edition', value: 'First Edition' },
  ];

  const handleAddToCart = () => {
    if (totalCart < maxCart) {
      addToCart();
    } else {
      setSnackbarCart(true);
    }
  };

  const CartSnackbar = () => {
    return (
      <Snackbar
        autoHideDuration={2000}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
        open={snackbarCart}
        onClose={() => setSnackbarCart(false)}
      >
        <Alert onClose={() => setSnackbarCart(false)} severity="warning" variant="filled">
        Maximum Cart Reached
        </Alert>
      </Snackbar>
    );
  };

  return (
    <Stack
      spacing={3}
      sx={{
        p: (theme) => ({
          md: theme.spacing(5, 5, 0, 2),
        }),
      }}
      {...other}
    >
      <Stack spacing={2}>
        <Typography variant="h5">{name}</Typography>
      </Stack>

      <Divider sx={{ borderStyle: 'dashed' }} />

      {details.map((item) => (
        <Stack key={item.label} direction="row" justifyContent="space-between">
          <Typography variant="subtitle2">{item.label}</Typography>
          <Typography variant="caption">{item.value}</Typography>
        </Stack>
      ))}

      <Stack direction="row" justifyContent="space-between">
        <Typography variant="subtitle2" sx={{ height: 36, lineHeight: '36px' }}>
          Stock
        </Typography>

        <Stack spacing={1}>
          <Typography
            variant="caption"
            component="div"
            sx={{ textAlign: 'right', color: 'text.secondary' }}
          >
            Available: {available}
          </Typography>
        </Stack>
      </Stack>

      <Divider sx={{ borderStyle: 'dashed' }} />

      <Stack direction="row" spacing={2}>
        <Button disabled={available > 0} fullWidth size="large" variant="outlined">
          Reserve
        </Button>
        <Button onClick={handleAddToCart} fullWidth size="large" variant="contained">
          Borrow
        </Button>
      </Stack>
      <CartSnackbar />
    </Stack>
  );
}
