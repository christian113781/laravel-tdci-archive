<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use ZipArchive;
use Exception;

class RestoreService
{
    protected BackupService $backupService;
    protected string $restoreTempRoot;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
        $this->restoreTempRoot = storage_path('app');
    }

    public function restoreSqlFile(UploadedFile $sqlFile, $user): array
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $tempDir = storage_path("app/temp_restore_sql_{$timestamp}");
        $this->ensureDirectory($tempDir);

        $sqlPath = $tempDir . '/restore.sql';
        $sqlFile->move($tempDir, 'restore.sql');

        $safetyBackup = $this->createSafetyBackup($timestamp);
        $this->enterMaintenanceMode();

        try {
            $summary = $this->restoreDatabaseFromSql($sqlPath);
            $this->clearCaches();
            $this->logActivity('sql_restore', $user, basename($sqlPath), 'success', $summary);

            return [
                'summary' => $summary,
                'safety_backup' => $safetyBackup,
            ];
        } catch (Exception $exception) {
            $this->logActivity('sql_restore', $user, basename($sqlPath), 'failed', [
                'error' => $exception->getMessage(),
            ]);

            if (file_exists($safetyBackup)) {
                try {
                    $this->restoreDatabaseFromSql($safetyBackup);
                    $this->logActivity('sql_restore', $user, basename($safetyBackup), 'rollback', [
                        'message' => 'Restored safety backup after failure',
                    ]);
                } catch (Exception $rollbackException) {
                    $this->logActivity('sql_restore', $user, basename($safetyBackup), 'rollback_failed', [
                        'error' => $rollbackException->getMessage(),
                    ]);
                }
            }

            throw $exception;
        } finally {
            $this->leaveMaintenanceMode();
            $this->deleteDirectory($tempDir);
        }
    }

    public function restoreBackupZip(UploadedFile $zipFile, $user, bool $restoreDatabase = false): array
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $tempDir = storage_path("app/temp_restore_zip_{$timestamp}");
        $this->ensureDirectory($tempDir);

        $zipPath = $tempDir . '/restore.zip';
        $zipFile->move($tempDir, 'restore.zip');

        $safetyBackup = null;
        if ($restoreDatabase) {
            $safetyBackup = $this->createSafetyBackup($timestamp);
        }

        $this->enterMaintenanceMode();

        try {
            $root = $this->safeExtractZip($zipPath, $tempDir);
            $rootPath = $tempDir . '/' . $root;
            $filesSummary = $this->restoreArchiveFiles($rootPath);
            $databaseSummary = null;

            if ($restoreDatabase) {
                $sqlFile = $this->findRestoreSqlFile($rootPath);
                if (!$sqlFile) {
                    throw new Exception('No database SQL file found in backup ZIP.');
                }
                $databaseSummary = $this->restoreDatabaseFromSql($sqlFile);
            }

            $this->clearCaches();
            $this->logActivity('backup_restore', $user, $zipFile->getClientOriginalName(), 'success', [
                'files' => $filesSummary,
                'database' => $databaseSummary,
            ]);

            return [
                'files' => $filesSummary,
                'database' => $databaseSummary,
                'safety_backup' => $safetyBackup,
            ];
        } catch (Exception $exception) {
            $this->logActivity('backup_restore', $user, $zipFile->getClientOriginalName(), 'failed', [
                'error' => $exception->getMessage(),
            ]);

            if ($restoreDatabase && $safetyBackup && file_exists($safetyBackup)) {
                try {
                    $this->restoreDatabaseFromSql($safetyBackup);
                    $this->logActivity('backup_restore', $user, basename($safetyBackup), 'rollback', [
                        'message' => 'Restored safety backup after failure',
                    ]);
                } catch (Exception $rollbackException) {
                    $this->logActivity('backup_restore', $user, basename($safetyBackup), 'rollback_failed', [
                        'error' => $rollbackException->getMessage(),
                    ]);
                }
            }

            throw $exception;
        } finally {
            $this->leaveMaintenanceMode();
            $this->deleteDirectory($tempDir);
        }
    }

    protected function safeExtractZip(string $zipPath, string $extractTo): string
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception('Unable to open backup ZIP file.');
        }

        $rootFolder = null;
        $entryCount = $zip->numFiles;

        for ($i = 0; $i < $entryCount; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $name = $stat['name'];
            if (strpos($name, '..') !== false) {
                $zip->close();
                throw new Exception('Zip archive contains invalid paths.');
            }

            $parts = explode('/', trim($name, '/'));
            if (!$rootFolder && count($parts) > 0) {
                $rootFolder = $parts[0];
            }
        }

        if (!$rootFolder) {
            $zip->close();
            throw new Exception('Invalid backup ZIP structure.');
        }

        for ($i = 0; $i < $entryCount; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $name = $stat['name'];
            if ($name === '' || rtrim($name, '/') === '') {
                continue;
            }

            $target = $extractTo . '/' . $name;
            $this->ensureDirectory(dirname($target));

            if (substr($name, -1) === '/') {
                $this->ensureDirectory($target);
                continue;
            }

            $stream = $zip->getStream($name);
            if ($stream === false) {
                $zip->close();
                throw new Exception("Unable to extract entry: {$name}");
            }

            $contents = stream_get_contents($stream);
            fclose($stream);
            file_put_contents($target, $contents);
        }

        $zip->close();

        return $rootFolder;
    }

    protected function restoreArchiveFiles(string $rootPath): array
    {
        $filesPath = $rootPath . '/files';
        if (!is_dir($filesPath)) {
            return [
                'restored' => false,
                'message' => 'No files directory found in backup.',
                'details' => [],
            ];
        }

        $allowed = [
            'archives' => storage_path('app/archives'),
            'public/archives' => storage_path('app/public/archives'),
        ];

        $details = [];
        $restoredAny = false;

        foreach ($allowed as $relative => $destination) {
            $source = $filesPath . '/' . $relative;
            if (!is_dir($source)) {
                continue;
            }

            if (is_dir($destination)) {
                $this->deleteDirectory($destination);
            }

            $this->copyDirectory($source, $destination);
            $restoredAny = true;

            $details[] = [
                'source' => $source,
                'destination' => $destination,
                'source_count' => $this->countFiles($source),
                'destination_count' => $this->countFiles($destination),
            ];
        }

        return [
            'restored' => $restoredAny,
            'details' => $details,
        ];
    }

    protected function restoreDatabaseFromSql(string $sqlFile): array
    {
        if (!file_exists($sqlFile)) {
            throw new Exception('SQL file not found for restore.');
        }

        $content = file_get_contents($sqlFile);
        $this->disableForeignKeyChecks();

        try {
            $this->dropAllTables();
            $execution = $this->executeSqlContent($content);
            $verification = $this->verifyDatabaseRestore($content);
            $this->enableForeignKeyChecks();

            return array_merge($execution, $verification);
        } catch (Exception $exception) {
            $this->enableForeignKeyChecks();
            throw $exception;
        }
    }

    protected function createSafetyBackup(string $timestamp): string
    {
        $this->ensureDirectory(storage_path('app/backups'));
        $backupPath = storage_path("app/backups/pre_restore_{$timestamp}.sql");
        $this->backupService->exportDatabaseToSql($backupPath);
        return $backupPath;
    }

    protected function disableForeignKeyChecks(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    protected function enableForeignKeyChecks(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function dropAllTables(): void
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $dbName;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            DB::statement("DROP TABLE IF EXISTS `{$tableName}`;");
        }
    }

    protected function executeSqlContent(string $sqlContent): array
    {
        $statements = $this->splitSqlStatements($sqlContent);
        $executed = 0;
        $errors = [];

        foreach ($statements as $index => $statement) {
            try {
                DB::unprepared($statement);
                $executed++;
            } catch (Exception $exception) {
                $errors[] = [
                    'index' => $index,
                    'message' => $exception->getMessage(),
                    'statement' => substr($statement, 0, 250),
                ];
            }
        }

        if (!empty($errors)) {
            throw new Exception('SQL restore failed with ' . count($errors) . ' statement errors.');
        }

        return [
            'statements' => count($statements),
            'executed' => $executed,
            'errors' => $errors,
        ];
    }

    protected function splitSqlStatements(string $sql): array
    {
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $lines = explode("\n", $sql);
        $statements = [];
        $buffer = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || preg_match('/^(--|#)/', $trimmed)) {
                continue;
            }

            if (preg_match('/^DELIMITER\s+/i', $trimmed)) {
                continue;
            }

            $buffer .= $line . "\n";
            if (substr($trimmed, -1) === ';') {
                $statements[] = trim($buffer);
                $buffer = '';
            }
        }

        if (trim($buffer) !== '') {
            $statements[] = trim($buffer);
        }

        return $statements;
    }

    protected function verifyDatabaseRestore(string $sqlContent): array
    {
        preg_match_all('/CREATE TABLE `([^`]+)`/i', $sqlContent, $tableMatches);
        $tables = array_unique($tableMatches[1] ?? []);
        $verification = [];
        $totalRecords = 0;
        $missing = [];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
                continue;
            }

            $count = DB::table($table)->count();
            $verification[$table] = $count;
            $totalRecords += $count;
        }

        return [
            'verified_tables' => count($tables) - count($missing),
            'missing_tables' => $missing,
            'record_counts' => $verification,
            'total_restored_records' => $totalRecords,
        ];
    }

    protected function findRestoreSqlFile(string $rootPath): ?string
    {
        $paths = [
            $rootPath . '/database.sql',
            $rootPath . '/database_backup.sql',
            $rootPath . '/files/database.sql',
            $rootPath . '/files/database_backup.sql',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function clearCaches(): void
    {
        Artisan::call('cache:clear', ['--quiet' => true]);
        Artisan::call('config:clear', ['--quiet' => true]);
        Artisan::call('route:clear', ['--quiet' => true]);
        Artisan::call('view:clear', ['--quiet' => true]);
    }

    protected function enterMaintenanceMode(): void
    {
        try {
            Artisan::call('down', ['--quiet' => true]);
        } catch (Exception $exception) {
            // If the app is already down or maintenance mode fails, continue.
        }
    }

    protected function leaveMaintenanceMode(): void
    {
        try {
            Artisan::call('up', ['--quiet' => true]);
        } catch (Exception $exception) {
            // If the app is already up or artisan fails, ignore.
        }
    }

    protected function logActivity(string $type, $user, string $filename, string $status, array $details = []): void
    {
        $payload = array_merge([
            'type' => $type,
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'filename' => $filename,
            'status' => $status,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ], $details);

        \Log::info('[RESTORE] Activity', $payload);
    }

    protected function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        $this->ensureDirectory($destination);

        $items = array_diff(scandir($source), ['.', '..']);

        foreach ($items as $item) {
            $sourcePath = $source . '/' . $item;
            $destinationPath = $destination . '/' . $item;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destinationPath);
            } else {
                copy($sourcePath, $destinationPath);
            }
        }
    }

    protected function countFiles(string $directory): int
    {
        if (!is_dir($directory)) {
            return 0;
        }

        $count = 0;
        $items = array_diff(scandir($directory), ['.', '..']);

        foreach ($items as $item) {
            $path = $directory . '/' . $item;
            if (is_dir($path)) {
                $count += $this->countFiles($path);
            } else {
                $count++;
            }
        }

        return $count;
    }
}
