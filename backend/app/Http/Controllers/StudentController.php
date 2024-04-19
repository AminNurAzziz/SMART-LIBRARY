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

        $result = $this->studentService->getStudentWithPeminjaman($nim);
        if (!$result) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json([
            'message' => 'Student data retrieved successfully',
            'student' => $result['student'],
            'data_peminjaman' => $result['data_peminjaman']
        ], 200);
    }
}
