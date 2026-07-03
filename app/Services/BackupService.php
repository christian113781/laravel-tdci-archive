<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ZipArchive;
use Exception;

class BackupService
{
    protected string $backupDir;
    protected array $archiveSources;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        $this->archiveSources = [
            storage_path('app/archives'),
            storage_path('app/public/archives'),
        ];
    }

    public function createFullBackup(): array
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $fileName = "system-backup-{$timestamp}.zip";
        $backupPath = $this->backupDir . '/' . $fileName;
        $tempDir = storage_path("app/temp_backup_{$timestamp}");

        $this->ensureDirectory($this->backupDir);
        $this->ensureDirectory($tempDir);

        $sqlFile = $tempDir . '/database.sql';
        $this->exportDatabaseToSql($sqlFile);
        $this->copyArchiveFiles($tempDir . '/files');
        $this->createZipArchive($tempDir, $backupPath);
        $this->deleteDirectory($tempDir);
        $this->cleanupOldBackups(3);

        return [
            'backup_path' => $backupPath,
            'backup_file_name' => $fileName,
        ];
    }

    protected function cleanupOldBackups(int $maxBackups = 3): void
    {
        if (!is_dir($this->backupDir)) {
            return;
        }

        $files = array_filter(scandir($this->backupDir), function ($item) {
            return is_file($this->backupDir . '/' . $item) && preg_match('/^system-backup-.*\.zip$/', $item);
        });

        if (count($files) <= $maxBackups) {
            return;
        }

        usort($files, function ($a, $b) {
            return filemtime($this->backupDir . '/' . $a) <=> filemtime($this->backupDir . '/' . $b);
        });

        $toDelete = array_slice($files, 0, count($files) - $maxBackups);

        foreach ($toDelete as $file) {
            @unlink($this->backupDir . '/' . $file);
        }
    }

    public function exportDatabaseToSql(string $sqlFile): array
    {
        $this->ensureDirectory(dirname($sqlFile));

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $dbName;

        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $totalRows = 0;
        $allTables = [];

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $allTables[] = $tableName;

            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (empty($create) || !isset($create[0]->{'Create Table'})) {
                throw new Exception("Failed to get CREATE TABLE statement for {$tableName}");
            }

            $sql .= "-- Table structure for table `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $create[0]->{'Create Table'} . ";\n\n";

            $rows = DB::select("SELECT * FROM `{$tableName}`");
            $rowCount = count($rows);
            if ($rowCount > 0) {
                $sql .= "-- Data for table `{$tableName}` ({$rowCount} rows)\n";
                $totalRows += $rowCount;

                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_map(function ($column) {
                        return "`{$column}`";
                    }, array_keys($rowArray));

                    $values = array_map(function ($value) {
                        return $this->quoteValue($value);
                    }, array_values($rowArray));

                    $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        file_put_contents($sqlFile, $sql);

        if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
            throw new Exception('Database export produced an empty SQL file.');
        }

        return [
            'sql_file' => $sqlFile,
            'file_size' => filesize($sqlFile),
            'tables' => count($allTables),
            'total_rows' => $totalRows,
            'users_included' => in_array('users', $allTables, true),
        ];
    }

    protected function copyArchiveFiles(string $filesDir): array
    {
        $this->ensureDirectory($filesDir);

        $copied = 0;
        $destinations = [];

        foreach ($this->archiveSources as $sourceDir) {
            if (!is_dir($sourceDir)) {
                continue;
            }

            $relativeSource = $this->getRelativeSourcePath($sourceDir);
            $destination = $filesDir . '/' . $relativeSource;
            $this->copyDirectory($sourceDir, $destination);
            $copied += $this->countFiles($destination);
            $destinations[] = $relativeSource;
        }

        return [
            'source_paths' => $this->archiveSources,
            'copied_files' => $copied,
            'destinations' => $destinations,
        ];
    }

    protected function getRelativeSourcePath(string $path): string
    {
        $base = str_replace('\\', '/', storage_path('app'));
        $source = str_replace('\\', '/', $path);
        $relative = ltrim(str_replace($base, '', $source), '/');
        return $relative;
    }

    protected function createZipArchive(string $sourceDir, string $zipPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Unable to create ZIP archive.');
        }

        $rootName = basename($sourceDir);
        $this->addDirectoryToZip($sourceDir, $zip, $rootName);
        $zip->close();

        if (!file_exists($zipPath)) {
            throw new Exception('ZIP archive was not created.');
        }
    }

    protected function addDirectoryToZip(string $dir, ZipArchive $zip, string $zipDir = ''): void
    {
        $handle = opendir($dir);

        if ($handle === false) {
            throw new Exception("Unable to open directory: {$dir}");
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . '/' . $file;
            $zipPath = $zipDir ? $zipDir . '/' . $file : $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($zipPath);
                $this->addDirectoryToZip($filePath, $zip, $zipPath);
            } else {
                $zip->addFile($filePath, $zipPath);
            }
        }

        closedir($handle);
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

    protected function quoteValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return DB::getPdo()->quote($value);
    }
}
