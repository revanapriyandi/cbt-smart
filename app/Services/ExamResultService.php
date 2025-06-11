<?php

namespace App\Services;

use App\Models\ExamResultModel;

class ExamResultService
{
    private $examResultModel;

    public function __construct()
    {
        $this->examResultModel = new ExamResultModel();
    }

    /**
     * Bulk delete exam results
     */
    public function bulkDelete(array $resultIds)
    {
        return $this->examResultModel->bulkDelete($resultIds);
    }

    /**
     * Bulk publish exam results
     */
    public function bulkPublish(array $resultIds)
    {
        return $this->examResultModel->bulkPublish($resultIds);
    }

    /**
     * Bulk unpublish exam results
     */
    public function bulkUnpublish(array $resultIds)
    {
        return $this->examResultModel->bulkUnpublish($resultIds);
    }

    /**
     * Bulk recalculate scores
     */
    public function bulkRecalculate(array $resultIds)
    {
        return $this->examResultModel->bulkRecalculate($resultIds);
    }

    /**
     * Recalculate a single result's score
     */
    public function recalculateScore(int $resultId)
    {
        return $this->examResultModel->recalculateScore($resultId);
    }

    /**
     * Publish results of a session
     */
    public function publishSessionResults(int $sessionId, ?string $message = null)
    {
        return $this->examResultModel->publishSessionResults($sessionId, $message);
    }

    /**
     * Generate report file and return filename
     */
    public function createReport(int $examId, ?int $classId = null, string $reportType = 'summary')
    {
        $data = $this->examResultModel->generateReport($examId, $classId, $reportType);

        if (!$data) {
            return null;
        }

        return $this->createReportFile($data, $reportType);
    }

    private function createReportFile(array $data, string $type)
    {
        $filename = 'report_' . $type . '_' . date('Y-m-d_H-i-s') . '.html';
        $filepath = WRITEPATH . 'uploads/reports/' . $filename;

        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $html = $this->generateReportHTML($data, $type);
        file_put_contents($filepath, $html);

        return $filename;
    }

    private function generateReportHTML(array $data, string $type): string
    {
        $html = "<!DOCTYPE html>
        <html>
        <head>
            <title>Laporan {$type}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f2f2f2; }
                .stats { display: flex; justify-content: space-around; margin-bottom: 30px; }
                .stat-box { text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
            </style>
        </head>
        <body>";

        switch ($type) {
            case 'summary':
                $html .= $this->generateSummaryReport($data);
                break;
            case 'detailed':
                $html .= $this->generateDetailedReport($data);
                break;
            case 'analysis':
                $html .= $this->generateAnalysisReport($data);
                break;
        }

        $html .= "</body></html>";

        return $html;
    }

    private function generateSummaryReport(array $data): string
    {
        $html = "<div class='header'>
            <h1>Laporan Ringkasan Hasil Ujian</h1>
            <p>Tanggal: " . date('d/m/Y H:i') . "</p>
        </div>";

        $html .= "<div class='stats'>
            <div class='stat-box'>
                <h3>{$data['total_participants']}</h3>
                <p>Total Peserta</p>
            </div>
            <div class='stat-box'>
                <h3>{$data['average_score']}</h3>
                <p>Rata-rata Skor</p>
            </div>
            <div class='stat-box'>
                <h3>{$data['pass_rate']}%</h3>
                <p>Tingkat Kelulusan</p>
            </div>
        </div>";

        return $html;
    }

    private function generateDetailedReport(array $data): string
    {
        return "<h1>Detailed Report</h1>";
    }

    private function generateAnalysisReport(array $data): string
    {
        return "<h1>Analysis Report</h1>";
    }
}
