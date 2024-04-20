// @mui
import {
  Card,
  Table,
  TableRow,
  TableBody,
  TableCell,
  CardProps,
  CardHeader,
  Typography,
  TableContainer,
} from '@mui/material';
// components
import Label from '../../../components/label';
import Scrollbar from '../../../components/scrollbar';
import { TableHeadCustom } from '../../../components/table';

// ----------------------------------------------------------------------

type RowProps = {
  bookCode: string;
  bookTitle: string;
  bookCategory: string;
  loanDate: Date;
  returnDate: Date;
};

interface Props extends CardProps {
  title?: string;
  subheader?: string;
  tableData: RowProps[];
  tableLabels: any;
}

export default function BorrowedBookTable({
  title,
  subheader,
  tableData,
  tableLabels,
  ...other
}: Props) {
  return (
    <Card {...other}>
      <CardHeader title={title} subheader={subheader} sx={{ mb: 3 }} />

      <TableContainer sx={{ overflow: 'unset' }}>
        <Scrollbar>
          <Table sx={{ minWidth: 720 }}>
            <TableHeadCustom headLabel={tableLabels} />

            <TableBody>
              {tableData.map((row) => (
                <BorrowedBookTableRow key={row.bookCode} row={row} />
              ))}
            </TableBody>
          </Table>
        </Scrollbar>
      </TableContainer>
    </Card>
  );
}

// ----------------------------------------------------------------------

type BorrowedBookTableRowProps = {
  row: RowProps;
};

function BorrowedBookTableRow({ row }: BorrowedBookTableRowProps) {
  return (
    <TableRow>
      <TableCell>{row.bookCode}</TableCell>
      <TableCell>{row.bookTitle}</TableCell>

      <TableCell align='center'>{row.bookCategory}</TableCell>

      {/* <TableCell>{row.loanDate.toString()}</TableCell> */}
      <TableCell align='center'>Sat Apr 20 2024</TableCell>

      {/* <TableCell>{row.returnDate.toString()}</TableCell> */}
      <TableCell align='center'>Mon Apr 22 2024</TableCell>

      <TableCell align="center">
        <Label variant="soft" color={'success'}>
          Extend
        </Label>
      </TableCell>
    </TableRow>
  );
}
