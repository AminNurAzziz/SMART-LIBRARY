export interface LoginPayload {
  username: string;
  password: string;
}

export interface AuthStudentState {
  user: StudentData | null;
  login: (user: StudentData) => void;
  logout: () => void;
}

export interface StudentBorrowedData {
  id: number;
  borrow_date: string;
  return_date: string;
  book_title: string;
  book_code: string;
  status: string;
}

export interface StudentData {
  profile: {
    id: number;
    nim: string;
    student_name: string;
    major: string;
    class: string;
    email: string;
    status: string;
    created_at: string;
    updated_at: string;
  };
  borrowedData: Array<StudentBorrowedData>;
}

export interface StudentAuthResponseData {
  data: {
    message: string;
    student: {
      id: number;
      nim: string;
      student_name: string;
      major: string;
      class: string;
      email: string;
      status: string;
      created_at: string;
      updated_at: string;
    };
    borrowed_data: [];
  };
}
