// @mui
import { Stack, Button, Divider, Typography } from '@mui/material';
// @types
import { IProduct } from '../../../../@types/product';
// _mock
// ----------------------------------------------------------------------

type Props = {
  product: IProduct;
};

export default function BookDetailSummary({ product, ...other }: Props) {
  const { name, available, category, tags,  } = product;

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

      <Stack direction="row" alignItems="center" justifyContent="space-between">
        <Typography variant="subtitle2">Category</Typography>
        <Typography variant="caption">{category}</Typography>
      </Stack>

      <Stack direction="row" justifyContent="space-between">
        <Typography variant="subtitle2" sx={{ height: 40, lineHeight: '40px', flexGrow: 1 }}>
          Author
        </Typography>
        <Typography variant="caption">{name}</Typography>
      </Stack>

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
        <Button fullWidth size="large" type="submit" variant="contained">
          Borrow
        </Button>
      </Stack>
    </Stack>
  );
}
