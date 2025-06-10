<?php

namespace App\Controllers\Admin;

use CodeIgniter\Files\File;

class AdminBackupController extends BaseAdminController
{
    protected $backupPath;

    public function __construct()
    {
        parent::__construct();
        $this->backupPath = WRITEPATH . 'backups/';

        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    /**
     * Display backup management page
     */
    public function index()
    {
        $data = [
            'backups' => $this->getBackupList(),
            'backupStats' => $this->getBackupStats()
        ];

        return view('admin/backup/index', $data);
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {            $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $this->backupPath . $fileName;

            // Get database configuration
            $dbConfig = new \Config\Database();
            $dbSettings = $dbConfig->default;

            // Create mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
                escapeshellarg($dbSettings['hostname']),
                escapeshellarg($dbSettings['username']),
                escapeshellarg($dbSettings['password']),
                escapeshellarg($dbSettings['database']),
                escapeshellarg($filePath)
            );

            // Execute backup command
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar === 0 && file_exists($filePath)) {
                // Log backup creation
                $this->userActivityLogModel->logActivity(
                    session()->get('user_id'),
                    'backup_create',
                    "Database backup created: {$fileName}",
                    $this->request->getIPAddress(),
                    $this->request->getUserAgent()
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Backup berhasil dibuat!',
                    'filename' => $fileName,
                    'size' => $this->formatFileSize(filesize($filePath))
                ]);
            } else {
                throw new \Exception('Backup command failed: ' . implode('\n', $output));
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup creation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $filePath = $this->backupPath . $filename;

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Backup file not found');
        }

        // Log backup download
        $this->userActivityLogModel->logActivity(
            session()->get('user_id'),
            'backup_download',
            "Downloaded backup: {$filename}",
            $this->request->getIPAddress(),
            $this->request->getUserAgent()
        );

        return $this->response->download($filePath, null);
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        try {
            $filePath = $this->backupPath . $filename;

            if (!file_exists($filePath)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan!'
                ]);
            }

            if (unlink($filePath)) {
                // Log backup deletion
                $this->userActivityLogModel->logActivity(
                    session()->get('user_id'),
                    'backup_delete',
                    "Deleted backup: {$filename}",
                    $this->request->getIPAddress(),
                    $this->request->getUserAgent()
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Backup berhasil dihapus!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus backup!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup deletion failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Restore database from backup
     */
    public function restoreBackup()
    {
        try {
            $filename = $this->request->getPost('filename');
            $filePath = $this->backupPath . $filename;            if (!file_exists($filePath)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan!'
                ]);
            }

            // Get database configuration
            $dbConfig = new \Config\Database();
            $dbSettings = $dbConfig->default;

            // Create mysql restore command
            $command = sprintf(
                'mysql --host=%s --user=%s --password=%s %s < %s',
                escapeshellarg($dbSettings['hostname']),
                escapeshellarg($dbSettings['username']),
                escapeshellarg($dbSettings['password']),
                escapeshellarg($dbSettings['database']),
                escapeshellarg($filePath)
            );

            // Execute restore command
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar === 0) {
                // Log backup restore
                $this->userActivityLogModel->logActivity(
                    session()->get('user_id'),
                    'backup_restore',
                    "Database restored from backup: {$filename}",
                    $this->request->getIPAddress(),
                    $this->request->getUserAgent()
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Database berhasil dipulihkan dari backup!'
                ]);
            } else {
                throw new \Exception('Restore command failed: ' . implode('\n', $output));
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup restore failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memulihkan database: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Upload backup file
     */
    public function uploadBackup()
    {
        try {
            $file = $this->request->getFile('backup_file');

            if (!$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid!'
                ]);
            }

            // Validate file extension
            if ($file->getExtension() !== 'sql') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya file SQL yang diizinkan!'
                ]);
            }

            // Generate unique filename
            $fileName = 'uploaded_' . date('Y-m-d_H-i-s') . '_' . $file->getName();

            if ($file->move($this->backupPath, $fileName)) {
                // Log backup upload
                $this->userActivityLogModel->logActivity(
                    session()->get('user_id'),
                    'backup_upload',
                    "Uploaded backup file: {$fileName}",
                    $this->request->getIPAddress(),
                    $this->request->getUserAgent()
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File backup berhasil diupload!',
                    'filename' => $fileName
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupload file backup!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Backup upload failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupload backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats()
    {
        try {
            $backups = $this->getBackupList();
            $totalSize = 0;

            foreach ($backups as $backup) {
                $totalSize += filesize($this->backupPath . $backup['filename']);
            }

            return [
                'total_backups' => count($backups),
                'total_size' => $this->formatFileSize($totalSize),
                'latest_backup' => !empty($backups) ? $backups[0]['created_at'] : null,
                'backup_path' => $this->backupPath
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting backup stats: ' . $e->getMessage());
            return [
                'total_backups' => 0,
                'total_size' => '0 B',
                'latest_backup' => null,
                'backup_path' => $this->backupPath
            ];
        }
    }

    /**
     * Get list of backup files
     */
    private function getBackupList()
    {
        $backups = [];

        if (is_dir($this->backupPath)) {
            $files = scandir($this->backupPath);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $this->backupPath . $file;
                    $backups[] = [
                        'filename' => $file,
                        'size' => $this->formatFileSize(filesize($filePath)),
                        'created_at' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'type' => strpos($file, 'uploaded_') === 0 ? 'uploaded' : 'generated'
                    ];
                }
            }

            // Sort by creation time (newest first)
            usort($backups, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return $backups;
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Clean old backups (keep only latest N backups)
     */
    public function cleanOldBackups()
    {
        try {
            $keepCount = (int) $this->request->getPost('keep_count', 5);
            $backups = $this->getBackupList();

            if (count($backups) <= $keepCount) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tidak ada backup lama yang perlu dihapus.',
                    'deleted_count' => 0
                ]);
            }

            $toDelete = array_slice($backups, $keepCount);
            $deletedCount = 0;

            foreach ($toDelete as $backup) {
                $filePath = $this->backupPath . $backup['filename'];
                if (file_exists($filePath) && unlink($filePath)) {
                    $deletedCount++;
                }
            }

            // Log cleanup activity
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'backup_cleanup',
                "Cleaned {$deletedCount} old backup files",
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} backup lama.",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Backup cleanup failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membersihkan backup lama: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get backup data for DataTables AJAX
     */
    public function data()
    {
        try {
            $backups = $this->getBackupList();
            
            // Format data for DataTables
            $formattedBackups = [];
            foreach ($backups as $backup) {
                $formattedBackups[] = [
                    'filename' => $backup['filename'],
                    'size' => $backup['size_formatted'],
                    'created_at' => date('Y-m-d H:i:s', $backup['created_at']),
                    'age' => $backup['age'],
                    'actions' => '' // Actions will be rendered by DataTable
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw') ?? 1),
                'data' => $formattedBackups,
                'recordsTotal' => count($formattedBackups),
                'recordsFiltered' => count($formattedBackups)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in backup data: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw') ?? 1),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => 'Failed to fetch backup data'
            ]);
        }
    }
}
