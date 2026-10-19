<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class WebsiteTransfer extends Command
{
    protected $signature = 'website:transfer {action : export or import} {--file= : Path to the transfer JSON file}';

    protected $description = 'Export or import website data and media for a fresh hosting database';

    private const TABLES = ['users', 'site_settings', 'jobs', 'news', 'applications'];

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/private/deployment/website.json');

        try {
            return match ($this->argument('action')) {
                'export' => $this->exportData($path),
                'import' => $this->importData($path),
                default => throw new RuntimeException('Use export or import.'),
            };
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function exportData(string $path): int
    {
        $this->call('media:import-website');
        $tables = DB::transaction(function () {
            $tables = [];
            foreach (self::TABLES as $table) {
                $tables[$table] = DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
                if ($table === 'users') {
                    foreach ($tables[$table] as &$user) {
                        $user['remember_token'] = null;
                    }
                    unset($user);
                }
            }

            return $tables;
        });
        $files = [];
        foreach (Storage::disk('public')->allFiles() as $name) {
            if (basename($name) !== '.gitignore') {
                $files[] = ['disk' => 'public', 'path' => $name, 'data' => base64_encode(Storage::disk('public')->get($name))];
            }
        }
        foreach (array_unique(array_column($tables['applications'], 'cv_path')) as $name) {
            if (! Storage::disk('local')->exists($name)) {
                throw new RuntimeException('Missing CV file: '.$name);
            }
            $files[] = ['disk' => 'local', 'path' => $name, 'data' => base64_encode(Storage::disk('local')->get($name))];
        }
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode(['version' => 1, 'tables' => $tables, 'files' => $files], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $this->info('Exported to '.$path);
        foreach ($tables as $table => $rows) {
            $this->line($table.': '.count($rows));
        }
        $this->line('Files: '.count($files));

        return self::SUCCESS;
    }

    private function importData(string $path): int
    {
        $data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        if (($data['version'] ?? null) !== 1 || array_keys($data['tables'] ?? []) !== self::TABLES || ! is_array($data['files'] ?? null)) {
            throw new RuntimeException('Invalid transfer file.');
        }
        foreach (self::TABLES as $table) {
            if (DB::table($table)->exists()) {
                throw new RuntimeException('Import stopped: '.$table.' already contains data. Use a fresh database and run migrate without seeding.');
            }
        }
        // Validate every file before writing anything. Existing different files are preserved.
        foreach ($data['files'] as $file) {
            $name = $file['path'] ?? '';
            if (! in_array($file['disk'] ?? '', ['public', 'local'], true)
                || ! is_string($name) || $name === '' || preg_match('#(^/|\\\\|:|(^|/)\.{1,2}(/|$)|\x00)#', $name)
                || ($file['disk'] === 'local' && ! str_starts_with($name, 'cvs/'))
                || base64_decode($file['data'] ?? '', true) === false) {
                throw new RuntimeException('Invalid media file in transfer package.');
            }
            $disk = Storage::disk($file['disk']);
            if ($disk->exists($name) && $disk->get($name) !== base64_decode($file['data'], true)) {
                throw new RuntimeException('Import stopped: existing file differs: '.$name);
            }
        }
        $written = [];
        try {
            DB::transaction(function () use ($data, &$written) {
                foreach (self::TABLES as $table) {
                    foreach ($data['tables'][$table] as $row) {
                        DB::table($table)->insert($row);
                    }
                }
                foreach ($data['files'] as $file) {
                    $disk = Storage::disk($file['disk']);
                    if (! $disk->exists($file['path'])) {
                        if (! $disk->put($file['path'], base64_decode($file['data'], true))) {
                            throw new RuntimeException('Could not write '.$file['path']);
                        }
                        $written[] = $file;
                    }
                }
            });
        } catch (\Throwable $e) {
            foreach ($written as $file) {
                Storage::disk($file['disk'])->delete($file['path']);
            }
            throw $e;
        }
        $this->info('Imported website data and '.count($data['files']).' files. Existing account passwords and permissions were preserved.');

        return self::SUCCESS;
    }
}
