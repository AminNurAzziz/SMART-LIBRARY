import {
  Card,
  Container,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableRow,
} from '@mui/material';
import { Helmet } from 'react-helmet-async';
import CustomBreadcrumbs from '@/components/custom-breadcrumbs';
import { PATH_STUDENT } from '@/routes/paths';
import Scrollbar from '@/components/scrollbar';
import { TableHeadCustom, TableNoData, TablePaginationCustom, useTable } from '@/components/table';
import { useState } from 'react';
import { IBooks, MOCK_BOOK_DATA } from '@/@types/books';
import Label from '@/components/label';

const TABLE_HEAD = [
  { id: 'book_code', label: 'Book Code' },
  { id: 'book_title', label: 'Book Title' },
  { id: 'status', label: 'Book Status', align: 'center' },
  { id: 'borrow_date', label: 'Loan Date', align: 'center' },
  { id: 'return_date', label: 'Return Date', align: 'center' },
  { id: 'action', label: 'Action', align: 'center' },
];

const BooksTableRow = ({ row }: { row: IBooks }) => {
  return (
    <TableRow hover>
      <TableCell>{row.book_code}</TableCell>

      <TableCell>{row.book_title}</TableCell>

      <TableCell align="center">{row.status}</TableCell>

      <TableCell align="center">{row.borrow_date || 'N/A'}</TableCell>

      <TableCell align="center">{row.return_date || 'N/A'}</TableCell>

      <TableCell align="center">
        <Label variant="soft" color="success">
          Extend
        </Label>
      </TableCell>
    </TableRow>
  );
};

function BorrowedHistoryPage() {
  const {
    dense,
    page,
    order,
    orderBy,
    rowsPerPage,
    setPage,
    //
    selected,
    setSelected,
    onSelectRow,
    onSelectAllRows,
    //
    onSort,
    onChangeDense,
    onChangePage,
    onChangeRowsPerPage,
  } = useTable({
    defaultOrderBy: 'createdAt',
  });

  const handleChangePage = async () => {
    
  }

  const [tableData, setTableData] = useState<IBooks[]>(MOCK_BOOK_DATA);
  const isNotFound = tableData.length === 0;

  return (
    <>
      <Helmet>
        <title> Student | Borrowed Books History List</title>
      </Helmet>
      <Container>
        <CustomBreadcrumbs
          heading="Borrowed Books History List"
          links={[
            { name: 'Dashboard', href: PATH_STUDENT.root },
            { name: 'Borrowed Books History' },
          ]}
        />

        <Card>
          <TableContainer sx={{ position: 'relative', overflow: 'unset' }}>
            <Scrollbar>
              <Table size={'medium'}>
                <TableHeadCustom
                  order={order}
                  orderBy={orderBy}
                  headLabel={TABLE_HEAD}
                  rowCount={tableData.length}
                  numSelected={selected.length}
                  onSort={onSort}
                  // onSelectAllRows={(checked) =>
                  //   onSelectAllRows(
                  //     checked,
                  //     tableData.map((row) => row.id)
                  //   )
                  // }
                />

                {MOCK_BOOK_DATA.map((row) => {
                  return <BooksTableRow key={row.id} row={row} />;
                })}

                <TableBody>
                  <TableNoData isNotFound={isNotFound} />
                </TableBody>
              </Table>
            </Scrollbar>
          </TableContainer>

          <TablePaginationCustom
            count={tableData.length}
            page={page}
            rowsPerPage={rowsPerPage}
            onPageChange={onChangePage}
            onRowsPerPageChange={onChangeRowsPerPage}
          />
        </Card>
      </Container>
    </>
  );
}

export default BorrowedHistoryPage;
