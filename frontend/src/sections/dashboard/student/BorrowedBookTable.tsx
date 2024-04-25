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
  id: number;
  book_code: string;
  book_title: string;
  status: string,
  borrow_date: string;
  return_date: string;
};

interface Props extends CardProps {
  title?: string;
  subheader?: string;
  tableData: RowProps[] | undefined;
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
                <BorrowedBookTableRow key={row.id} row={row} />
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
      <TableCell>{row.book_code}</TableCell>
      <TableCell>{row.book_title}</TableCell>

      <TableCell align='center'>{row.status}</TableCell>

      {/* <TableCell>{row.loanDate.toString()}</TableCell> */}
      <TableCell align='center'>{row.borrow_date}</TableCell>

      {/* <TableCell>{row.returnDate.toString()}</TableCell> */}
      <TableCell align='center'>{row.return_date}</TableCell>

      <TableCell align="center">
        <Label variant="soft" color={'success'}>
          Extend
        </Label>
      </TableCell>
    </TableRow>
  );
}
