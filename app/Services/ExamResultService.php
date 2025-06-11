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
}
