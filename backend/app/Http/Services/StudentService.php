<?php

namespace App\Http\Services;

use App\Models\Borrowing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class StudentService
{
    public function getStudentWithBorrowingData($nim)
    {
        if (!$nim) {
            return null;
        }

        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            Log::warning("Student not found for NIM: {$nim}");
            return null;
        }

        // $data_peminjaman = Peminjaman::select('peminjaman.id', 'peminjaman.tgl_pinjam', 'peminjaman.tgl_kembali', 'bukus.judul_buku', 'bukus.kode_buku', 'peminjaman.status')
        //     ->join('buku_peminjaman', 'peminjaman.kode_pinjam', '=', 'buku_peminjaman.kode_pinjam')
        //     ->join('bukus', 'buku_peminjaman.kode_buku', '=', 'bukus.kode_buku')
        //     ->where('peminjaman.nim', $nim)
        //     ->where('peminjaman.status', 'Dipinjam')
        //     ->limit(2)
        //     ->get();

        $data_peminjaman = Borrowing::select('borrowing_book.id', 'borrowing_book.loan_date', 'borrowing_book.return_date', 'book.title_book', 'book.code_book', 'borrowing_book.status')
            ->join('borrowing_book', 'borrowing.code_borrow', '=', 'borrowing_book.code_borrow')
            ->join('book', 'borrowing_book.code_book', '=', 'book.code_book')
            ->where('borrowing.nim', $nim)
            ->where('borrowing_book.status', 'Dipinjam')
            ->limit(2)
            ->get();


        return [
            'student' => $student,
            'borrowing_data' => $data_peminjaman
        ];
    }

    public function createStudent(array $data)
    {
        $validator = Validator::make($data, [
            'nim' => 'required',
            'name' => 'required',
            'major' => 'required',
            'class' => 'required',
            'email' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $student = Student::create([
            'nim' => $data['nim'],
            'name' => $data['name'],
            'major' => $data['major'],
            'class' => $data['class'],
            'email' => $data['email'],
            'status' => $data['status'],
        ]);

        Log::info("Student created with NIM: {$data['nim']}");

        return $student;
    }

    public function updateStudent(array $data)
    {
        $validator = Validator::make($data, [
            'nim' => 'required',
            'name' => 'required',
            'major' => 'required',
            'class' => 'required',
            'email' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $student = Student::where('nim', $data['nim'])->first();
        if (!$student) {
            Log::warning("Student not found for NIM: {$data['nim']}");
            return null;
        }

        $student->update([
            'name' => $data['name'],
            'major' => $data['major'],
            'class' => $data['class'],
            'email' => $data['email'],
            'status' => $data['status'],
        ]);

        Log::info("Student updated with NIM: {$data['nim']}");

        return $student;
    }

    public function deleteStudent($nim)
    {
        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            Log::warning("Student not found for NIM: {$nim}");
            return null;
        }

        $student->delete();
        Log::info("Student deleted with NIM: {$nim}");
        return $student;
    }
}
