<?php

namespace App\Controllers\Admin;

use App\Models\ExamSessionModel;
use App\Models\ExamModel;
use App\Models\ClassModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminExamSessionController extends BaseAdminController
{
    protected $examSessionModel;
    protected $examModel;
    protected $classModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->examSessionModel = new ExamSessionModel();
        $this->examModel = new ExamModel();
        $this->classModel = new ClassModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Sesi Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Sesi Ujian', 'url' => '/admin/exam-sessions']
            ]
        ];

        // Get filter parameters
        $filters = [
            'status' => $this->request->getGet('status'),
            'exam_id' => $this->request->getGet('exam_id'),
            'class_id' => $this->request->getGet('class_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        // Get exam sessions with filters
        $examSessions = $this->examSessionModel->getSessionsWithDetails($filters);

        // Get statistics
        $statistics = $this->examSessionModel->getSessionStatistics();        // Get filter options
        $data['examSessions'] = $examSessions;
        $data['statistics'] = $statistics;
        $data['exams'] = $this->examModel->findAll();
        $data['classes'] = $this->classModel->findAll();
        $data['filters'] = $filters; // Pass filter values to view

        return view('admin/exam-sessions/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->store();
        }

        $data = [
            'title' => 'Buat Sesi Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Sesi Ujian', 'url' => '/admin/exam-sessions'],
                ['title' => 'Buat Sesi Ujian', 'url' => '/admin/exam-sessions/create']
            ],
            'exams' => $this->examModel->findAll(),
            'classes' => $this->classModel->findAll()
        ];

        return view('admin/exam-sessions/create', $data);
    }

    public function store()
    {
        $rules = [
            'exam_id' => 'required|is_natural_no_zero',
            'class_id' => 'required|is_natural_no_zero',
            'session_name' => 'required|max_length[100]',
            'start_time' => 'required|valid_date[Y-m-d H:i:s]',
            'end_time' => 'required|valid_date[Y-m-d H:i:s]',
            'max_participants' => 'required|is_natural_no_zero',
            'room_location' => 'permit_empty|max_length[100]',
            'instructions' => 'permit_empty|max_length[1000]',
            'security_settings' => 'permit_empty|in_list[strict,normal,relaxed]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'exam_id' => $this->request->getPost('exam_id'),
            'class_id' => $this->request->getPost('class_id'),
            'session_name' => $this->request->getPost('session_name'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'max_participants' => $this->request->getPost('max_participants'),
            'room_location' => $this->request->getPost('room_location'),
            'instructions' => $this->request->getPost('instructions'),
            'security_settings' => $this->request->getPost('security_settings') ?: 'normal',
            'status' => 'scheduled',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Check for conflicts
        $conflicts = $this->examSessionModel->checkConflicts($data);
        if (!empty($conflicts)) {
            return redirect()->back()->withInput()->with('error', 'Konflik terdeteksi: ' . implode(', ', $conflicts));
        }

        if ($this->examSessionModel->save($data)) {
            // Log activity
            $this->logActivity('exam_session_created', "Sesi ujian '{$data['session_name']}' berhasil dibuat");

            return redirect()->to('/admin/exam-sessions')->with('success', 'Sesi ujian berhasil dibuat');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal membuat sesi ujian');
    }

    public function show($id)
    {
        $examSession = $this->examSessionModel->getSessionWithDetails($id);
        if (!$examSession) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sesi ujian dengan ID {$id} tidak ditemukan");
        }

        // Get participants
        $participants = $this->examSessionModel->getSessionParticipants($id);

        // Get session progress
        $progress = $this->examSessionModel->getSessionProgress($id);

        $data = [
            'title' => 'Detail Sesi Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Sesi Ujian', 'url' => '/admin/exam-sessions'],
                ['title' => $examSession->session_name, 'url' => "/admin/exam-sessions/{$id}"]
            ],
            'examSession' => $examSession,
            'participants' => $participants,
            'progress' => $progress
        ];

        return view('admin/exam-sessions/view', $data);
    }

    public function edit($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sesi ujian dengan ID {$id} tidak ditemukan");
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->update($id);
        }

        $data = [
            'title' => 'Edit Sesi Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Sesi Ujian', 'url' => '/admin/exam-sessions'],
                ['title' => 'Edit Sesi Ujian', 'url' => "/admin/exam-sessions/{$id}/edit"]
            ],
            'examSession' => $examSession,
            'exams' => $this->examModel->findAll(),
            'classes' => $this->classModel->findAll()
        ];

        return view('admin/exam-sessions/edit', $data);
    }

    public function update($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sesi ujian dengan ID {$id} tidak ditemukan");
        }

        // Check if session can be edited
        if (in_array($examSession['status'], ['active', 'completed'])) {
            return redirect()->back()->with('error', 'Sesi ujian yang sedang aktif atau selesai tidak dapat diedit');
        }

        $rules = [
            'exam_id' => 'required|is_natural_no_zero',
            'class_id' => 'required|is_natural_no_zero',
            'session_name' => 'required|max_length[100]',
            'start_time' => 'required|valid_date[Y-m-d H:i:s]',
            'end_time' => 'required|valid_date[Y-m-d H:i:s]',
            'max_participants' => 'required|is_natural_no_zero',
            'room_location' => 'permit_empty|max_length[100]',
            'instructions' => 'permit_empty|max_length[1000]',
            'security_settings' => 'permit_empty|in_list[strict,normal,relaxed]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'exam_id' => $this->request->getPost('exam_id'),
            'class_id' => $this->request->getPost('class_id'),
            'session_name' => $this->request->getPost('session_name'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'max_participants' => $this->request->getPost('max_participants'),
            'room_location' => $this->request->getPost('room_location'),
            'instructions' => $this->request->getPost('instructions'),
            'security_settings' => $this->request->getPost('security_settings') ?: 'normal',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check for conflicts (excluding current session)
        $conflicts = $this->examSessionModel->checkConflicts($data, $id);
        if (!empty($conflicts)) {
            return redirect()->back()->withInput()->with('error', 'Konflik terdeteksi: ' . implode(', ', $conflicts));
        }

        if ($this->examSessionModel->update($id, $data)) {
            // Log activity
            $this->logActivity('exam_session_updated', "Sesi ujian '{$data['session_name']}' berhasil diperbarui");

            return redirect()->to('/admin/exam-sessions')->with('success', 'Sesi ujian berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui sesi ujian');
    }

    public function delete($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        // Check if session can be deleted
        if (in_array($examSession['status'], ['active', 'completed'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian yang sedang aktif atau selesai tidak dapat dihapus']);
        }

        if ($this->examSessionModel->delete($id)) {
            // Log activity
            $this->logActivity('exam_session_deleted', "Sesi ujian '{$examSession['session_name']}' berhasil dihapus");

            return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian berhasil dihapus']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus sesi ujian']);
    }

    public function start($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        if ($examSession['status'] !== 'scheduled') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya sesi ujian yang dijadwalkan yang dapat dimulai']);
        }

        $data = [
            'status' => 'active',
            'actual_start_time' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examSessionModel->update($id, $data)) {
            // Log activity
            $this->logActivity('exam_session_started', "Sesi ujian '{$examSession['session_name']}' dimulai");

            return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian berhasil dimulai']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memulai sesi ujian']);
    }

    public function end($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        if ($examSession['status'] !== 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya sesi ujian yang aktif yang dapat diakhiri']);
        }

        $data = [
            'status' => 'completed',
            'actual_end_time' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examSessionModel->update($id, $data)) {
            // Log activity
            $this->logActivity('exam_session_ended', "Sesi ujian '{$examSession['session_name']}' diakhiri");

            return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian berhasil diakhiri']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengakhiri sesi ujian']);
    }

    public function monitor($id)
    {
        $examSession = $this->examSessionModel->getSessionWithDetails($id);
        if (!$examSession) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Sesi ujian dengan ID {$id} tidak ditemukan");
        }

        // Get real-time monitoring data
        $participants = $this->examSessionModel->getSessionParticipants($id);
        $progress = $this->examSessionModel->getSessionProgress($id);
        $activities = $this->examSessionModel->getSessionActivities($id);

        $data = [
            'title' => 'Monitor Sesi Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Sesi Ujian', 'url' => '/admin/exam-sessions'],
                ['title' => 'Monitor', 'url' => "/admin/exam-sessions/{$id}/monitor"]
            ],
            'examSession' => $examSession,
            'participants' => $participants,
            'progress' => $progress,
            'activities' => $activities
        ];

        return view('admin/exam-sessions/monitor', $data);
    }

    public function export()
    {
        $format = $this->request->getGet('format') ?: 'excel';
        $filters = [
            'status' => $this->request->getGet('status'),
            'exam_id' => $this->request->getGet('exam_id'),
            'class_id' => $this->request->getGet('class_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $examSessions = $this->examSessionModel->getSessionsWithDetails($filters);

        if ($format === 'excel') {
            return $this->exportToExcel($examSessions);
        }

        return $this->exportToPDF($examSessions);
    }

    private function exportToExcel($examSessions)
    {
        $filename = 'exam_sessions_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create spreadsheet content
        $data = [
            ['ID', 'Nama Sesi', 'Ujian', 'Kelas', 'Waktu Mulai', 'Waktu Selesai', 'Max Peserta', 'Lokasi', 'Status', 'Dibuat Oleh', 'Dibuat Pada']
        ];

        foreach ($examSessions as $session) {
            $data[] = [
                $session->id,
                $session->session_name,
                $session->exam_title,
                $session->class_name,
                $session->start_time,
                $session->end_time,
                $session->max_participants,
                $session->room_location ?: '-',
                ucfirst($session->status),
                $session->creator_name,
                $session->created_at
            ];
        }

        // Output CSV format for simplicity
        foreach ($data as $row) {
            echo implode("\t", $row) . "\n";
        }
    }

    private function exportToPDF($examSessions)
    {
        $filename = 'exam_sessions_' . date('Y-m-d_H-i-s') . '.pdf';

        // For simplicity, let's create a basic HTML table that can be saved as PDF
        // In a real application, you might want to use a PDF library like TCPDF or DOMPDF

        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Laporan Sesi Ujian</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #333; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Sesi Ujian</h1>
        <p>Digenerate pada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Sesi</th>
                <th>Ujian</th>
                <th>Kelas</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Max Peserta</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Dibuat Oleh</th>
                <th>Dibuat Pada</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($examSessions as $session) {
            $html .= '<tr>
                <td>' . $session->id . '</td>
                <td>' . htmlspecialchars($session->session_name) . '</td>
                <td>' . htmlspecialchars($session->exam_title) . '</td>
                <td>' . htmlspecialchars($session->class_name) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($session->start_time)) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($session->end_time)) . '</td>
                <td>' . $session->max_participants . '</td>
                <td>' . htmlspecialchars($session->room_location ?: '-') . '</td>
                <td>' . ucfirst($session->status) . '</td>
                <td>' . htmlspecialchars($session->creator_name) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($session->created_at)) . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
</body>
</html>';

        // Set headers for PDF download
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo $html;
        return;
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $sessionIds = $this->request->getPost('session_ids');

        if (empty($sessionIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal satu sesi ujian']);
        }

        $result = false;
        $message = '';

        switch ($action) {
            case 'delete':
                $result = $this->examSessionModel->bulkDelete($sessionIds);
                $message = $result ? 'Sesi ujian berhasil dihapus' : 'Gagal menghapus sesi ujian';
                break;
            case 'start':
                $result = $this->examSessionModel->bulkStart($sessionIds);
                $message = $result ? 'Sesi ujian berhasil dimulai' : 'Gagal memulai sesi ujian';
                break;
            case 'end':
                $result = $this->examSessionModel->bulkEnd($sessionIds);
                $message = $result ? 'Sesi ujian berhasil diakhiri' : 'Gagal mengakhiri sesi ujian';
                break;
            default:
                return $this->response->setJSON(['success' => false, 'message' => 'Aksi tidak valid']);
        }

        if ($result) {
            $this->logActivity('exam_session_bulk_action', "Bulk action '{$action}' dilakukan pada " . count($sessionIds) . " sesi ujian");
        }

        return $this->response->setJSON(['success' => $result, 'message' => $message]);
    }

    public function getSessionData($id)
    {
        $examSession = $this->examSessionModel->getSessionWithDetails($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        $progress = $this->examSessionModel->getSessionProgress($id);
        $participants = $this->examSessionModel->getSessionParticipants($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'session' => $examSession,
                'progress' => $progress,
                'participants' => $participants
            ]
        ]);
    }

    public function getMonitorData($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        $participants = $this->examSessionModel->getSessionParticipants($id);
        $progress = $this->examSessionModel->getSessionProgress($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'participants' => $participants,
                'progress' => $progress
            ]
        ]);
    }

    public function getRecentActivities($id)
    {
        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        $activities = $this->examSessionModel->getSessionActivities($id, 20); // Get last 20 activities

        return $this->response->setJSON([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function getParticipantDetails($sessionId, $participantId)
    {
        $examSession = $this->examSessionModel->find($sessionId);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        $participant = $this->examSessionModel->getParticipantDetails($sessionId, $participantId);
        if (!$participant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Peserta tidak ditemukan']);
        }

        // Generate HTML for participant details
        $html = '
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <p class="text-sm text-gray-900">' . esc($participant->student_name) . '</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIS</label>
                    <p class="text-sm text-gray-900">' . esc($participant->student_nis) . '</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-' .
            ($participant->status === 'completed' ? 'green' : ($participant->status === 'in_progress' ? 'yellow' : 'gray')) .
            '-100 text-' .
            ($participant->status === 'completed' ? 'green' : ($participant->status === 'in_progress' ? 'yellow' : 'gray')) .
            '-800">' . ucfirst(str_replace('_', ' ', $participant->status)) . '</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Progress</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: ' . $participant->progress . '%"></div>
                        </div>
                        <span class="text-sm text-gray-600">' . $participant->progress . '%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <p class="text-sm text-gray-900">' . ($participant->started_at ? date('d/m/Y H:i', strtotime($participant->started_at)) : '-') . '</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <p class="text-sm text-gray-900">' . ($participant->completed_at ? date('d/m/Y H:i', strtotime($participant->completed_at)) : '-') . '</p>
                </div>
            </div>';

        if (isset($participant->answers) && !empty($participant->answers)) {
            $html .= '
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jawaban</label>
                <div class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <div class="grid grid-cols-5 gap-2">';

            foreach ($participant->answers as $index => $answer) {
                $html .= '
                        <div class="flex items-center justify-center w-8 h-8 rounded text-xs font-medium ' .
                    ($answer->is_correct ? 'bg-green-100 text-green-800' : ($answer->answer ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) . '">
                            ' . ($index + 1) . '
                        </div>';
            }

            $html .= '
                    </div>
                    <div class="mt-3 text-xs text-gray-600">
                        <span class="inline-flex items-center mr-4">
                            <div class="w-3 h-3 bg-green-100 rounded mr-1"></div>
                            Benar
                        </span>
                        <span class="inline-flex items-center mr-4">
                            <div class="w-3 h-3 bg-red-100 rounded mr-1"></div>
                            Salah
                        </span>
                        <span class="inline-flex items-center">
                            <div class="w-3 h-3 bg-gray-100 rounded mr-1"></div>
                            Belum dijawab
                        </span>
                    </div>
                </div>
            </div>';
        }

        $html .= '</div>';

        return $this->response->setJSON([
            'success' => true,
            'html' => $html
        ]);
    }

    public function sendWarning($id)
    {
        $data = $this->request->getJSON(true);
        $participantId = $data['participant_id'] ?? null;
        $message = $data['message'] ?? 'Harap perhatikan waktu yang tersisa dan fokus pada ujian.';

        if (!$participantId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID peserta tidak valid']);
        }

        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        // Send warning to participant (you can implement websocket or other real-time communication here)
        // For now, we'll log the warning activity
        $participant = $this->userModel->find($participantId);
        if ($participant) {
            $this->logActivity('exam_warning_sent', "Peringatan dikirim kepada {$participant['full_name']} dalam sesi '{$examSession['session_name']}'");

            // Here you could implement actual warning delivery mechanism
            // e.g., WebSocket, push notification, etc.

            return $this->response->setJSON(['success' => true, 'message' => 'Peringatan berhasil dikirim']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Peserta tidak ditemukan']);
    }

    public function forceSubmit($id)
    {
        $data = $this->request->getJSON(true);
        $participantId = $data['participant_id'] ?? null;

        if (!$participantId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID peserta tidak valid']);
        }

        $examSession = $this->examSessionModel->find($id);
        if (!$examSession) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi ujian tidak ditemukan']);
        }

        // Force submit the participant's exam
        $result = $this->examSessionModel->forceSubmitParticipant($id, $participantId);

        if ($result) {
            $participant = $this->userModel->find($participantId);
            if ($participant) {
                $this->logActivity('exam_force_submit', "Ujian {$participant['full_name']} dipaksa submit dalam sesi '{$examSession['session_name']}'");
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Ujian peserta berhasil dipaksa submit']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memaksa submit ujian peserta']);
    }
}
