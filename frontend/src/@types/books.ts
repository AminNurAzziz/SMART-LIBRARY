export type IBooks = {
  id: number;
  book_code: string;
  book_title: string;
  status: string;
  borrow_date: string;
  return_date: string;
};

export const MOCK_BOOK_DATA: IBooks[] = [
  {
    id: 1,
    book_code: 'ABC123',
    book_title: 'The Great Gatsby',
    status: 'Available',
    borrow_date: '2024-04-20',
    return_date: '2024-05-10',
  },
  {
    id: 2,
    book_code: 'DEF456',
    book_title: 'To Kill a Mockingbird',
    status: 'Borrowed',
    borrow_date: '2024-04-15',
    return_date: '2024-05-05',
  },
  {
    id: 3,
    book_code: 'GHI789',
    book_title: '1984',
    status: 'Available',
    borrow_date: '',
    return_date: '',
  },
  {
    id: 4,
    book_code: 'JKL012',
    book_title: 'Pride and Prejudice',
    status: 'Available',
    borrow_date: '',
    return_date: '',
  },
  {
    id: 5,
    book_code: "MNO345",
    book_title: "Harry Potter and the Sorcerer's Stone",
    status: "Available",
    borrow_date: "",
    return_date: "",
  }
];
