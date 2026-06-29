<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use App\Services\RestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    protected BackupService $backupService;
    protected RestoreService $restoreService;

    public function __construct(BackupService $backupService, RestoreService $restoreService)
    {
        $this->backupService = $backupService;
        $this->restoreService = $restoreService;
    }

    public function backup()
    {
        try {
            Log::info('[BACKUP] Starting full system backup');
            $result = $this->backupService->createFullBackup();

            Log::info('[BACKUP] Backup created successfully', [
                'path' => $result['backup_path'],
                'file' => $result['backup_file_name'],
            ]);

            return response()->download($result['backup_path'], $result['backup_file_name']);
        } catch (\Exception $e) {
            Log::error('[BACKUP] Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:1048576',
        ]);

        try {
            $result = $this->restoreService->restoreBackupZip($request->file('backup_file'), Auth::user(), false);
            $response = [
                'message' => 'Backup file restored successfully.',
                'summary' => $result,
            ];

            Log::info('[RESTORE] File-only restore completed', $response);

            if ($this->isJsonRequest($request)) {
                return response()->json($response);
            }

            return redirect()->back()->with('success', $response['message']);
        } catch (\Exception $e) {
            Log::error('[RESTORE] File-only restore failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->isJsonRequest($request)) {
                return response()->json([
                    'message' => 'Restore failed: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function restoreFull(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:1048576',
        ]);

        try {
            $result = $this->restoreService->restoreBackupZip($request->file('backup_file'), Auth::user(), true);
            $response = [
                'message' => 'Backup restored successfully with database and files.',
                'summary' => $result,
            ];

            Log::info('[RESTORE] Full backup restore completed', $response);

            if ($this->isJsonRequest($request)) {
                return response()->json($response);
            }

            return redirect()->back()->with('success', $response['message']);
        } catch (\Exception $e) {
            Log::error('[RESTORE] Full backup restore failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->isJsonRequest($request)) {
                return response()->json([
                    'message' => 'Full restore failed: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Full restore failed: ' . $e->getMessage());
        }
    }

    public function restoreSql(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:51200',
        ]);

        try {
            $result = $this->restoreService->restoreSqlFile($request->file('sql_file'), Auth::user());
            $response = [
                'message' => 'SQL restore completed successfully.',
                'summary' => $result['summary'] ?? null,
                'safety_backup' => $result['safety_backup'] ?? null,
            ];

            Log::info('[RESTORE SQL] Completed successfully', $response);

            if ($this->isJsonRequest($request)) {
                return response()->json($response);
            }

            return redirect()->back()->with('success', $response['message']);
        } catch (\Exception $e) {
            Log::error('[RESTORE SQL] Restore failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->isJsonRequest($request)) {
                return response()->json([
                    'message' => 'SQL restore failed: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'SQL restore failed: ' . $e->getMessage());
        }
    }

    protected function isJsonRequest(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || str_contains($request->header('accept', ''), 'application/json');
    }
}

