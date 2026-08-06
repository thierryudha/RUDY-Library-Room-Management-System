<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingMember;
use App\Models\Department;
use App\Models\Facility;
use App\Models\Feedback;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Room;
use App\Models\Staff;
use App\Models\StaffUnit;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'role' => 'admin'],
            ['id' => 2, 'role' => 'mahasiswa'],
            ['id' => 3, 'role' => 'dosen'],
            ['id' => 4, 'role' => 'staff'],
        ]);

        Department::insert([
            ['id' => 1, 'department_name' => 'Teknik Informatika'],
            ['id' => 2, 'department_name' => 'Sistem Informasi'],
            ['id' => 3, 'department_name' => 'Teknik Elektro'],
        ]);

        StudyProgram::insert([
            ['id' => 1, 'department_id' => 1, 'program_name' => 'D3 Teknik Informatika'],
            ['id' => 2, 'department_id' => 1, 'program_name' => 'D4 Teknik Informatika'],
            ['id' => 3, 'department_id' => 2, 'program_name' => 'D3 Sistem Informasi'],
            ['id' => 4, 'department_id' => 2, 'program_name' => 'D4 Sistem Informasi'],
            ['id' => 5, 'department_id' => 3, 'program_name' => 'D3 Teknik Elektro'],
        ]);

        StaffUnit::insert([
            ['id' => 1, 'unit_name' => 'UPT Perpustakaan'],
            ['id' => 2, 'unit_name' => 'Bagian Akademik'],
            ['id' => 3, 'unit_name' => 'Bagian Sarana Prasarana'],
        ]);

        Facility::insert([
            ['id' => 1, 'facility_name' => 'Proyektor'],
            ['id' => 2, 'facility_name' => 'AC'],
            ['id' => 3, 'facility_name' => 'Papan Tulis'],
            ['id' => 4, 'facility_name' => 'WiFi'],
            ['id' => 5, 'facility_name' => 'Komputer'],
            ['id' => 6, 'facility_name' => 'Sound System'],
            ['id' => 7, 'facility_name' => 'Kursi Rapat'],
            ['id' => 8, 'facility_name' => 'TV'],
        ]);

        $admin = User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@rudy.test',
            'password' => bcrypt('password'),
            'phone' => '081234567890',
            'role_id' => 1,
            'user_status' => 'active',
        ]);

        $staff = User::create([
            'name' => 'Staff Perpustakaan',
            'email' => 'staff@rudy.test',
            'password' => bcrypt('password'),
            'phone' => '081234567891',
            'role_id' => 4,
            'user_status' => 'active',
        ]);

        Staff::create([
            'user_id' => $staff->id,
            'employee_id_number' => 'STF001',
            'unit_id' => 1,
        ]);

        $dosen = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'dosen@rudy.test',
            'password' => bcrypt('password'),
            'phone' => '081234567892',
            'role_id' => 3,
            'user_status' => 'active',
        ]);

        Lecturer::create([
            'user_id' => $dosen->id,
            'employee_id_number' => 'DSN001',
            'department_id' => 1,
        ]);

        $mahasiswa = User::create([
            'name' => 'Rudy Mahasiswa',
            'email' => 'mahasiswa@rudy.test',
            'password' => bcrypt('password'),
            'phone' => '081234567893',
            'role_id' => 2,
            'user_status' => 'active',
        ]);

        Student::create([
            'user_id' => $mahasiswa->id,
            'student_id_number' => 'MHS001',
            'study_program_id' => 2,
            'class_of' => 2023,
            'activation_proof_path' => 'activation/mhs001.jpg',
        ]);

        $rooms = [
            Room::create(['room_name' => 'Ruang Baca 1', 'min_capacity' => 2, 'max_capacity' => 10, 'location' => 'Lantai 1 - Gedung Utama', 'room_image_path' => null]),
            Room::create(['room_name' => 'Ruang Diskusi A', 'min_capacity' => 3, 'max_capacity' => 8, 'location' => 'Lantai 2 - Gedung Utama', 'room_image_path' => null]),
            Room::create(['room_name' => 'Aula Mini', 'min_capacity' => 10, 'max_capacity' => 30, 'location' => 'Lantai 3 - Gedung Utama', 'room_image_path' => null]),
            Room::create(['room_name' => 'Ruang Seminar', 'min_capacity' => 15, 'max_capacity' => 50, 'location' => 'Lantai 4 - Gedung Utama', 'room_image_path' => null]),
            Room::create(['room_name' => 'Ruang Kolaborasi', 'min_capacity' => 5, 'max_capacity' => 12, 'location' => 'Lantai 2 - Gedung Sayap', 'room_image_path' => null]),
        ];

        $rooms[0]->facilities()->sync([1 => ['description' => null], 2 => ['description' => null], 4 => ['description' => 'WiFi 100Mbps']]);
        $rooms[1]->facilities()->sync([2 => ['description' => null], 3 => ['description' => null], 7 => ['description' => null]]);
        $rooms[2]->facilities()->sync([1 => ['description' => null], 2 => ['description' => null], 6 => ['description' => null], 8 => ['description' => null]]);
        $rooms[3]->facilities()->sync([1 => ['description' => 'Proyektor 4K'], 2 => ['description' => null], 4 => ['description' => 'WiFi 200Mbps'], 6 => ['description' => null], 8 => ['description' => null]]);
        $rooms[4]->facilities()->sync([2 => ['description' => null], 4 => ['description' => null], 5 => ['description' => '5 unit PC'], 7 => ['description' => null]]);

        $booking = Booking::create([
            'room_id' => $rooms[0]->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(3),
            'created_by_user_id' => $mahasiswa->id,
            'booking_status' => 'approved',
        ]);

        BookingMember::create([
            'booking_id' => $booking->id,
            'user_id' => $mahasiswa->id,
            'created_at' => now(),
        ]);

        Feedback::create([
            'booking_id' => $booking->id,
            'user_id' => $mahasiswa->id,
            'rating' => 4,
            'comment' => 'Ruangan nyaman, AC dingin, WiFi kencang.',
        ]);
    }
}
