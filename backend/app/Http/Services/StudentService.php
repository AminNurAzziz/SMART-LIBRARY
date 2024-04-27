<?php

namespace App\Http\Services;

use App\Models\Student;
use App\Models\Peminjaman;
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

        $data_peminjaman = Peminjaman::select('buku_peminjaman.id', 'buku_peminjaman.tgl_pinjam', 'buku_peminjaman.tgl_kembali', 'bukus.judul_buku', 'bukus.kode_buku', 'buku_peminjaman.status')
            ->join('buku_peminjaman', 'peminjaman.kode_pinjam', '=', 'buku_peminjaman.kode_pinjam')
            ->join('bukus', 'buku_peminjaman.kode_buku', '=', 'bukus.kode_buku')
            ->where('peminjaman.nim', $nim)
            ->where('buku_peminjaman.status', 'Dipinjam')
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
            'nama_mhs' => $data['name'],
            'prodi_mhs' => $data['major'],
            'kelas_mhs' => $data['class'],
            'email_mhs' => $data['email'],
            'status_mhs' => $data['status'],
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
            'nama_mhs' => $data['name'],
            'prodi_mhs' => $data['major'],
            'kelas_mhs' => $data['class'],
            'email_mhs' => $data['email'],
            'status_mhs' => $data['status'],
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
