<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function getStudentStatuses(Request $request)
    {
        $nim = $request->query('nim');
        if (!$nim) {
            return response()->json(['message' => 'NIM is required'], 400);
        }

        $result = $this->studentService->getStudentWithBorrowingData($nim);
        if (!$result) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $studentData = $result['student'];
        $borrowedData = $result['borrowing_data'];

        // Transforming keys of student data
        $studentData = [
            'id' => $studentData['id'],
            'nim' => $studentData['nim'],
            'student_name' => $studentData['nama_mhs'],
            'major' => $studentData['prodi_mhs'],
            'class' => $studentData['kelas_mhs'],
            'email' => $studentData['email_mhs'],
            'status' => $studentData['status_mhs'],
            'created_at' => $studentData['created_at'],
            'updated_at' => $studentData['updated_at']
        ];
        // Transforming keys of borrowed data
        $borrowedData = array_map(function ($data) {
            return [
                'id' => $data['id'],
                'borrow_date' => $data['tgl_pinjam'],
                'return_date' => $data['tgl_kembali'],
                'book_title' => $data['judul_buku'],
                'book_code' => $data['kode_buku'],
                'status' => $data['status']
            ];
        }, $borrowedData->toArray());


        return response()->json([
            'message' => 'Student data retrieved successfully',
            'student' => $studentData,
            'borrowed_data' => $borrowedData
        ], 200);
    }
}
