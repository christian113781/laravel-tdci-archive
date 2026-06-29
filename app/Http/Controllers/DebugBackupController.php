<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugBackupController extends Controller
{
    /**
     * Show backup/restore debug information
     */
    public function debugInfo()
    {
        $archivesPath = storage_path('app/public/archives');
        $backupsPath = storage_path('backups');
        $storagePath = storage_path();
        $logPath = storage_path('logs/laravel.log');

        $archiveCount = 0;
        $archives = [];
        if (is_dir($archivesPath)) {
            $items = array_diff(scandir($archivesPath), ['.', '..']);
            $archiveCount = count($items);
            $archives = array_values($items);
        }

        $backupFiles = [];
        if (is_dir($backupsPath)) {
            $files = array_diff(scandir($backupsPath), ['.', '..']);
            foreach ($files as $file) {
                $backupFiles[] = [
                    'name' => $file,
                    'size' => $this->formatBytes(filesize($backupsPath . '/' . $file)),
                    'date' => date('Y-m-d H:i:s', filemtime($backupsPath . '/' . $file))
                ];
            }
        }

        $tempFiles = [];
        $dirContents = array_diff(scandir($storagePath), ['.', '..']);
        foreach ($dirContents as $file) {
            if (strpos($file, 'temp_restore_') === 0 || strpos($file, 'temp_backup_') === 0) {
                $tempFiles[] = [
                    'name' => $file,
                    'date' => date('Y-m-d H:i:s', filemtime($storagePath . '/' . $file))
                ];
            }
        }

        // Get last 100 lines of log
        $logLines = [];
        if (file_exists($logPath)) {
            $lines = file($logPath);
            $logLines = array_slice($lines, -100);
        }

        $html = '<pre style="background: #f4f4f4; padding: 20px; font-family: monospace;">';
        $html .= '<h2>Backup & Restore Debug Info</h2>';
        $html .= '<hr>';
        
        $html .= '<h3>Archives Directory</h3>';
        $html .= '<strong>Path:</strong> ' . $archivesPath . '<br>';
        $html .= '<strong>Count:</strong> ' . $archiveCount . '<br>';
        if (count($archives) > 0) {
            $html .= '<strong>Contents:</strong><br>';
            foreach ($archives as $archive) {
                $html .= '  - ' . $archive . '<br>';
            }
        }
        
        $html .= '<hr>';
        $html .= '<h3>Backup Files</h3>';
        if (count($backupFiles) > 0) {
            foreach ($backupFiles as $backup) {
                $html .= '' . $backup['name'] . ' (' . $backup['size'] . ') - ' . $backup['date'] . '<br>';
            }
        } else {
            $html .= '<em>No backup files found</em><br>';
        }
        
        $html .= '<hr>';
        $html .= '<h3>Temporary Files</h3>';
        if (count($tempFiles) > 0) {
            foreach ($tempFiles as $temp) {
                $html .= '⚠️  ' . $temp['name'] . ' - ' . $temp['date'] . '<br>';
            }
            $html .= '<br>';
            $html .= '<form method="POST" action="' . route('admin.debug.backup.clear-temp') . '" style="display: inline;">';
            $html .= csrf_field();
            $html .= '<button type="submit" style="padding: 8px 15px; background: #ff6b6b; color: white; border: none; border-radius: 4px; cursor: pointer;">Clear Temp Files</button>';
            $html .= '</form>';
        } else {
            $html .= '<em>No temporary files</em><br>';
        }
        
        $html .= '<hr>';
        $html .= '<h3>Recent Logs (last 100 lines)</h3>';
        if (count($logLines) > 0) {
            $html .= implode('', $logLines);
        } else {
            $html .= '<em>No log entries</em>';
        }
        
        $html .= '</pre>';
        
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Clear all temp files
     */
    public function clearTempFiles()
    {
        $storagePath = storage_path();
        $dirContents = scandir($storagePath);
        $deletedCount = 0;

        foreach ($dirContents as $file) {
            if (strpos($file, 'temp_restore_') === 0 || strpos($file, 'temp_backup_') === 0) {
                $filePath = $storagePath . '/' . $file;
                if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
                $deletedCount++;
            }
        }

        return redirect()->back()->with('success', "Cleared {$deletedCount} temp file(s)");
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}