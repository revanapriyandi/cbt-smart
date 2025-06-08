<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ExamResultAccessFilter
 * 
 * Enhanced security filter for exam result access control.
 * Ensures that students can only access their own exam results,
 * while admins and teachers can access all results for management purposes.
 */
class ExamResultAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();        // Check if user is logged in
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
        $userRole = $session->get('user_role');  // Fixed: use 'user_role' instead of 'role'
        $userId = $session->get('user_id');

        // Allow admins and teachers to access all exam results without restrictions
        if (in_array($userRole, ['admin', 'teacher'])) {
            return;
        }

        // Extract exam ID or user ID from the URL segments
        $uri = $request->getUri();
        $segments = $uri->getSegments();

        // For routes like /admin/exam-results/{examId} or /admin/users/exam-performance/{userId}
        if (count($segments) >= 3) {
            $targetId = end($segments); // Get the last segment (ID)

            // If user is a student, they can only access their own exam results
            if ($userRole === 'student') {
                // For exam performance routes, check if the target user ID matches current user
                if (in_array('exam-performance', $segments) || in_array('result-detail', $segments)) {
                    if ($targetId != $userId) {
                        return redirect()->to('/student/dashboard')
                            ->with('error', 'You can only access your own exam results.');
                    }
                }

                // For exam results routes, check if student has taken the exam
                if (in_array('exam-results', $segments)) {
                    $examResultModel = new \App\Models\ExamResultModel();
                    $userResult = $examResultModel->where([
                        'exam_id' => $targetId,
                        'student_id' => $userId  // Fixed: use 'student_id' instead of 'user_id'
                    ])->first();
                    if (!$userResult) {
                        return redirect()->to('/student/dashboard')
                            ->with('error', 'You have not taken this exam or results are not available.');
                    }
                }
            }
        }

        // If we reach here, it means the user is a student and all student checks have passed
        // or it's an unhandled case - allow access
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
