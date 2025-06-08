<?php

namespace App\Controllers\Admin;

class AdminDashboardController extends BaseAdminController
{
    public function index()
    {
        $recentExams = $this->examModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->getExamsWithDetails();

        // Calculate status for recent exams
        $now = new \DateTime();
        foreach ($recentExams as &$exam) {
            $start = new \DateTime($exam['start_time']);
            $end = new \DateTime($exam['end_time']);

            if (!$exam['is_active']) {
                $exam['status'] = 'draft';
            } elseif ($now < $start) {
                $exam['status'] = 'upcoming';
            } elseif ($now >= $start && $now <= $end) {
                $exam['status'] = 'active';
            } else {
                $exam['status'] = 'completed';
            }
        }

        $data = [
            'totalUsers'    => $this->userModel->countAllResults(),
            'totalSubjects' => $this->subjectModel->countAllResults(),
            'totalExams'    => $this->examModel->countAllResults(),
            'activeExams'   => count($this->examModel->getActiveExams()),
            'recentUsers'   => $this->userModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll(),
            'recentExams'   => $recentExams,
        ];

        return view('admin/dashboard', $data);
    }
}
