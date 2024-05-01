<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Services\StudentService;
use Illuminate\Validation\ValidationException;

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
        Log::info("Student data retrieved successfully" . json_encode($result));

        $studentData = $result['student'];
        $borrowedData = $result['borrowing_data'];

        // Transforming keys of student data
        $studentData = [
            'id' => $studentData['id'],
            'nim' => $studentData['nim'],
            'student_name' => $studentData['name'],
            'major' => $studentData['major'],
            'class' => $studentData['class'],
            'email' => $studentData['email'],
            'status' => $studentData['status'],
        ];
        // Transforming keys of borrowed data
        $borrowedData = array_map(function ($data) {
            return [
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

    public function createStudent(Request $request)
    {
        try {
            $student = $this->studentService->createStudent($request->all());

            return response()->json([
                'message' => 'Create Student Successfully',
                'status' => 'success',
                'code' => 201,
                'data' => [
                    'student' => [
                        'nim' => $student->nim,
                        'name' => $student->name,
                        'major' => $student->major,
                        'class' => $student->class,
                        'email' => $student->email,
                        'status' => $student->status,
                    ],
                ],
            ], 201);
        } catch (ValidationException $e) {
            Log::error("Failed to create student: {$e->getMessage()}");
            $errorsDetails = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorsDetails[] = [
                        'msg' => $message,
                        'path' => $field,
                    ];
                }
            }

            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Invalid Request Parameter',
                'error' => 'Bad Request',
                'errors' => $errorsDetails,
            ], 400);
        } catch (\Exception $e) {
            Log::error("Failed to create student: {$e->getMessage()}");
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => 'Internal Server Error',
            ], 500);
        }
    }

    public function updateStudent(Request $request)
    {
        try {
            $student = $this->studentService->updateStudent($request->all());

            return response()->json([
                'message' => 'Update Student Successfully',
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'nim' => $student->nim,
                    'name' => $student->name,
                    'major' => $student->major,
                    'class' => $student->class,
                    'email' => $student->email,
                    'status' => $student->status,
                ],
            ], 200);
        } catch (ValidationException $e) {
            Log::error("Failed to update student: {$e->getMessage()}");
            $errorsDetails = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorsDetails[] = [
                        'msg' => $message,
                        'path' => $field,
                    ];
                }
            }

            return response()->json([
                'statusCode' => 400,
                'status' => false,
                'message' => 'Invalid Request Parameter',
                'error' => 'Bad Request',
                'errors' => $errorsDetails,
            ], 400);
        } catch (\Exception $e) {
            Log::error("Failed to update student: {$e->getMessage()}");
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => 'Internal Server Error',
            ], 500);
        }
    }

    public function  deleteStudent($nim)
    {
        $student = $this->studentService->deleteStudent($nim);
        return response()->json([
            'message' => 'Delete Student Successfully',
            'status' => 'success',
            'code' => 200,
            'data' => [
                'nim' => $student->nim,
                'name' => $student->name,
                'major' => $student->major,
                'class' => $student->class,
                'email' => $student->email,
                'status' => $student->status,
            ],
        ], 200);
    }
}
