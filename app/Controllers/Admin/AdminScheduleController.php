<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\ClassModel;
use App\Models\SubjectModel;
use App\Models\UserModel;
use App\Models\AcademicYearModel;

class AdminScheduleController extends BaseController
{
    protected $scheduleModel;
    protected $classModel;
    protected $subjectModel;
    protected $userModel;
    protected $academicYearModel;

    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->classModel = new ClassModel();
        $this->subjectModel = new SubjectModel();
        $this->userModel = new UserModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    public function index()
    {
        // Get statistics
        $stats = [
            'total' => $this->scheduleModel->countAll(),
            'active' => $this->scheduleModel->where('status', 'active')->countAllResults(),
            'today' => $this->scheduleModel->getTodaySchedulesCount(),
            'this_week' => $this->scheduleModel->getThisWeekSchedulesCount()
        ];        // Get filter options
        $classes = $this->classModel->where('is_active', 1)->findAll();
        $subjects = $this->subjectModel->findAll();
        $teachers = $this->userModel->where('role', 'teacher')->where('is_active', 1)->findAll();
        $academicYears = $this->academicYearModel->where('is_active', 1)->findAll();

        return view('admin/schedules/index', [
            'title' => 'Manajemen Jadwal',
            'stats' => $stats,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academicYears' => $academicYears
        ]);
    }
    public function getData()
    {
        // Disable debugging for AJAX requests to prevent HTML injection
        if ($this->request->isAJAX()) {
            // Clear any previous output
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Set proper headers
            $this->response->setHeader('Content-Type', 'application/json');
        }

        $request = $this->request;

        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';

        // Filters
        $classFilter = $request->getPost('class_filter');
        $subjectFilter = $request->getPost('subject_filter');
        $teacherFilter = $request->getPost('teacher_filter');
        $statusFilter = $request->getPost('status_filter');
        $dayFilter = $request->getPost('day_filter');
        $academicYearFilter = $request->getPost('academic_year_filter');

        $builder = $this->scheduleModel->getSchedulesWithDetails();

        // Apply filters
        if (!empty($classFilter)) {
            $builder->where('schedules.class_id', $classFilter);
        }
        if (!empty($subjectFilter)) {
            $builder->where('schedules.subject_id', $subjectFilter);
        }
        if (!empty($teacherFilter)) {
            $builder->where('schedules.teacher_id', $teacherFilter);
        }
        if (!empty($statusFilter)) {
            $builder->where('schedules.status', $statusFilter);
        }
        if (!empty($dayFilter)) {
            $builder->where('schedules.day_of_week', $dayFilter);
        }
        if (!empty($academicYearFilter)) {
            $builder->where('schedules.academic_year_id', $academicYearFilter);
        }

        // Search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('classes.name', $searchValue)
                ->orLike('subjects.name', $searchValue)
                ->orLike('users.full_name', $searchValue)
                ->orLike('schedules.room', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->scheduleModel->countAll();
        $filteredRecords = $builder->countAllResults(false);

        $schedules = $builder->limit($length, $start)
            ->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($schedules as $schedule) {
            $data[] = [
                'id' => $schedule['id'],
                'academic_year' => $schedule['academic_year_name'],
                'class_name' => $schedule['class_name'],
                'subject_name' => $schedule['subject_name'],
                'teacher_name' => $schedule['teacher_name'],
                'day_of_week' => $this->getDayName($schedule['day_of_week']),
                'time_range' => date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time'])),
                'room' => $schedule['room'] ?? '-',
                'status' => $schedule['status'],
                'created_at' => date('d/m/Y H:i', strtotime($schedule['created_at']))
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }
    public function create()
    {
        $classes = $this->classModel->where('is_active', 1)->findAll();
        $subjects = $this->subjectModel->findAll();
        $teachers = $this->userModel->where('role', 'teacher')->where('is_active', 1)->findAll();
        $academicYears = $this->academicYearModel->where('is_active', 1)->findAll();

        return view('admin/schedules/create', [
            'title' => 'Tambah Jadwal',
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academicYears' => $academicYears
        ]);
    }
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'academic_year_id' => 'required|integer',
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'day_of_week' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[7]',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'permit_empty|max_length[100]',
            'notes' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        // Calculate duration
        $startTime = strtotime($data['start_time']);
        $endTime = strtotime($data['end_time']);
        $data['duration'] = ($endTime - $startTime) / 60; // in minutes

        // Check for schedule conflicts
        if ($this->scheduleModel->hasConflict($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal bertabrakan dengan jadwal yang sudah ada'
            ]);
        }

        $data['created_by'] = session()->get('user_id');

        if ($this->scheduleModel->insert($data)) {
            // Log activity
            $this->scheduleModel->logActivity(
                $this->scheduleModel->getInsertID(),
                'create',
                'Jadwal baru ditambahkan',
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menambahkan jadwal'
        ]);
    }

    public function show($id)
    {
        $schedule = $this->scheduleModel->getScheduleWithDetails($id);

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }

        // Get related schedules (same class or teacher)
        $relatedSchedules = $this->scheduleModel->getRelatedSchedules($id, $schedule['class_id'], $schedule['teacher_id']);

        return view('admin/schedules/view', [
            'title' => 'Detail Jadwal',
            'schedule' => $schedule,
            'relatedSchedules' => $relatedSchedules
        ]);
    }

    public function edit($id)
    {
        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }
        $classes = $this->classModel->where('is_active', 1)->findAll();
        $subjects = $this->subjectModel->findAll();
        $teachers = $this->userModel->where('role', 'teacher')->where('is_active', 1)->findAll();
        $academicYears = $this->academicYearModel->where('is_active', 1)->findAll();

        return view('admin/schedules/edit', [
            'title' => 'Edit Jadwal',
            'schedule' => $schedule,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academicYears' => $academicYears
        ]);
    }

    public function update($id)
    {
        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ]);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'academic_year_id' => 'required|integer',
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'day_of_week' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[7]',
            'start_time' => 'required',
            'end_time' => 'required',
            'room' => 'permit_empty|max_length[100]',
            'notes' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,inactive,suspended]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        // Calculate duration
        $startTime = strtotime($data['start_time']);
        $endTime = strtotime($data['end_time']);
        $data['duration'] = ($endTime - $startTime) / 60; // in minutes

        // Check for schedule conflicts (excluding current schedule)
        if ($this->scheduleModel->hasConflict($data, $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal bertabrakan dengan jadwal yang sudah ada'
            ]);
        }

        $data['updated_by'] = session()->get('user_id');

        if ($this->scheduleModel->update($id, $data)) {
            // Log activity
            $this->scheduleModel->logActivity(
                $id,
                'update',
                'Jadwal diperbarui',
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui jadwal'
        ]);
    }

    public function delete($id)
    {
        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ]);
        }

        if ($this->scheduleModel->delete($id)) {
            // Log activity
            $this->scheduleModel->logActivity(
                $id,
                'delete',
                'Jadwal dihapus',
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus jadwal'
        ]);
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih jadwal terlebih dahulu'
            ]);
        }

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($this->scheduleModel->update($id, ['status' => 'active', 'updated_by' => session()->get('user_id')])) {
                            $count++;
                            $this->scheduleModel->logActivity($id, 'activate', 'Jadwal diaktifkan (bulk)', session()->get('user_id'));
                        }
                        break;
                    case 'deactivate':
                        if ($this->scheduleModel->update($id, ['status' => 'inactive', 'updated_by' => session()->get('user_id')])) {
                            $count++;
                            $this->scheduleModel->logActivity($id, 'deactivate', 'Jadwal dinonaktifkan (bulk)', session()->get('user_id'));
                        }
                        break;
                    case 'delete':
                        if ($this->scheduleModel->delete($id)) {
                            $count++;
                            $this->scheduleModel->logActivity($id, 'delete', 'Jadwal dihapus (bulk)', session()->get('user_id'));
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Jadwal ID $id: " . $e->getMessage();
            }
        }

        $message = '';
        switch ($action) {
            case 'activate':
                $message = "$count jadwal berhasil diaktifkan";
                break;
            case 'deactivate':
                $message = "$count jadwal berhasil dinonaktifkan";
                break;
            case 'delete':
                $message = "$count jadwal berhasil dihapus";
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    public function export()
    {
        $format = $this->request->getGet('format') ?? 'excel';

        $schedules = $this->scheduleModel->getSchedulesWithDetails()->get()->getResultArray();

        if ($format === 'excel') {
            return $this->exportToExcel($schedules);
        }

        return $this->exportToPDF($schedules);
    }

    private function exportToExcel($schedules)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Tahun Ajaran',
            'C1' => 'Kelas',
            'D1' => 'Mata Pelajaran',
            'E1' => 'Guru',
            'F1' => 'Hari',
            'G1' => 'Waktu',
            'H1' => 'Ruangan',
            'I1' => 'Durasi (menit)',
            'J1' => 'Status',
            'K1' => 'Dibuat'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Data
        $row = 2;
        foreach ($schedules as $index => $schedule) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $schedule['academic_year_name']);
            $sheet->setCellValue('C' . $row, $schedule['class_name']);
            $sheet->setCellValue('D' . $row, $schedule['subject_name']);
            $sheet->setCellValue('E' . $row, $schedule['teacher_name']);
            $sheet->setCellValue('F' . $row, $this->getDayName($schedule['day_of_week']));
            $sheet->setCellValue('G' . $row, date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time'])));
            $sheet->setCellValue('H' . $row, $schedule['room'] ?? '-');
            $sheet->setCellValue('I' . $row, $schedule['duration']);
            $sheet->setCellValue('J' . $row, ucfirst($schedule['status']));
            $sheet->setCellValue('K' . $row, date('d/m/Y H:i', strtotime($schedule['created_at'])));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'jadwal_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getWeeklySchedule()
    {
        $classId = $this->request->getGet('class_id');
        $teacherId = $this->request->getGet('teacher_id');
        $builder = $this->scheduleModel->getSchedulesWithDetails();
        $builder->where('schedules.status', 'active');

        if ($classId) {
            $builder->where('schedules.class_id', $classId);
        }

        if ($teacherId) {
            $builder->where('schedules.teacher_id', $teacherId);
        }

        $schedules = $builder->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->get()
            ->getResultArray();

        // Group schedules by day
        $weeklySchedule = [];
        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

        foreach ($schedules as $schedule) {
            $dayName = $dayNames[$schedule['day_of_week']] ?? 'Unknown';
            if (!isset($weeklySchedule[$dayName])) {
                $weeklySchedule[$dayName] = [];
            }

            $weeklySchedule[$dayName][] = [
                'id' => $schedule['id'],
                'subject_name' => $schedule['subject_name'],
                'class_name' => $schedule['class_name'],
                'teacher_name' => $schedule['teacher_name'],
                'time_range' => date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time'])),
                'room' => $schedule['room'] ?? '-'
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $weeklySchedule
        ]);
    }

    // Debug method to check data state
    public function debugData()
    {
        $db = \Config\Database::connect();

        $data = [
            'academic_years' => $db->table('academic_years')->get()->getResultArray(),
            'schedules' => $db->table('schedules')->get()->getResultArray(),
            'classes' => $db->table('classes')->get()->getResultArray(),
            'subjects' => $db->table('subjects')->get()->getResultArray(),
            'teachers' => $db->table('users')->where('role', 'teacher')->get()->getResultArray(),
        ];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'counts' => [
                'academic_years' => count($data['academic_years']),
                'schedules' => count($data['schedules']),
                'classes' => count($data['classes']),
                'subjects' => count($data['subjects']),
                'teachers' => count($data['teachers'])
            ]
        ]);
    }    // Temporary method to populate test data
    public function populateTestData()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Clear existing data
        $db->table('schedules')->truncate();
        $db->table('academic_years')->truncate();
        $db->table('classes')->truncate();
        $db->table('subjects')->truncate();

        // Clear users but keep admin
        $db->query('DELETE FROM users WHERE role != "admin"');

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        // 1. Insert subjects
        $subjects = [
            [
                'code' => 'MTK',
                'name' => 'Matematika',
                'description' => 'Mata pelajaran Matematika',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'BIN',
                'name' => 'Bahasa Indonesia',
                'description' => 'Mata pelajaran Bahasa Indonesia',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'IPA',
                'name' => 'Ilmu Pengetahuan Alam',
                'description' => 'Mata pelajaran IPA',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'IPS',
                'name' => 'Ilmu Pengetahuan Sosial',
                'description' => 'Mata pelajaran IPS',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('subjects')->insertBatch($subjects);

        // 2. Insert classes
        $classes = [
            [
                'code' => 'X-IPA-1',
                'name' => 'X IPA 1',
                'description' => 'Kelas X IPA 1',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'X-IPA-2',
                'name' => 'X IPA 2',
                'description' => 'Kelas X IPA 2',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'X-IPS-1',
                'name' => 'X IPS 1',
                'description' => 'Kelas X IPS 1',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('classes')->insertBatch($classes);

        // 3. Insert teachers
        $teachers = [
            [
                'username' => 'teacher1',
                'email' => 'teacher1@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Budi Santoso, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher2',
                'email' => 'teacher2@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Siti Nurhaliza, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher3',
                'email' => 'teacher3@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Ahmad Wijaya, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('users')->insertBatch($teachers);

        // 4. Insert academic years
        $academicYears = [
            [
                'code' => '2023-2024',
                'name' => '2023-2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => '2024-2025',
                'name' => '2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => '2025-2026',
                'name' => '2025-2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('academic_years')->insertBatch($academicYears);

        // Get the active academic year
        $activeAcademicYear = $db->table('academic_years')->where('is_active', true)->get()->getRowArray();
        $academicYearId = $activeAcademicYear['id'];

        // 5. Insert schedules
        $schedules = [
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 1, // Matematika
                'teacher_id' => 1, // Budi Santoso
                'day_of_week' => 1, // Monday                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas A',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 2, // Bahasa Indonesia
                'teacher_id' => 2, // Siti Nurhaliza
                'day_of_week' => 2, // Tuesday                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'room' => 'Ruang Kelas A',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 2, // X IPA 2
                'subject_id' => 1, // Matematika
                'teacher_id' => 1, // Budi Santoso
                'day_of_week' => 3, // Wednesday                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas B',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 2, // X IPA 2
                'subject_id' => 3, // IPA
                'teacher_id' => 3, // Ahmad Wijaya
                'day_of_week' => 4, // Thursday                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'room' => 'Lab IPA',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 3, // X IPS 1
                'subject_id' => 4, // IPS
                'teacher_id' => 2, // Siti Nurhaliza
                'day_of_week' => 5, // Friday                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas C',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 3, // IPA
                'teacher_id' => 3, // Ahmad Wijaya
                'day_of_week' => 1, // Monday                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'room' => 'Lab IPA',
                'status' => 'inactive', // Inactive for testing
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('schedules')->insertBatch($schedules);

        // Return success message
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Comprehensive test data populated successfully!',
            'data' => [
                'subjects' => count($subjects),
                'classes' => count($classes),
                'teachers' => count($teachers),
                'academic_years' => count($academicYears),
                'schedules' => count($schedules)
            ]
        ]);
    }

    private function getDayName($dayNumber)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        return $days[$dayNumber] ?? 'Unknown';
    }

    private function exportToPDF($schedules)
    {
        // Implementation would use a PDF library like TCPDF or mPDF
        // For now, return simple text format
        $filename = 'jadwal_' . date('Y-m-d_H-i-s') . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        echo "PDF export not implemented yet";
    }
}
