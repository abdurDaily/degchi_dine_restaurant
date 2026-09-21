<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ConvertToWebp extends Command
{
    protected $signature = 'images:convert-webp
                            {--dry-run : Show what would be done without making changes}
                            {--force : Re-convert images that already have a WebP version}
                            {--rollback : Rollback the last conversion using the backup JSON}';

    protected $description = 'Convert uploaded images to WebP format and update database references';

    protected int $webpQuality = 90;
    protected int $maxWidth = 1600;

    public function handle(): int
    {
        if ($this->option('rollback')) {
            return $this->rollback();
        }

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $publicDir = str_replace('\\', '/', public_path('')) . '/';
        $uploadDirs = [
            $publicDir . 'uploads',
        ];
        $storageDir = $publicDir . 'storage/profile_images';

        if (is_dir($storageDir)) {
            $uploadDirs[] = $storageDir;
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'bmp'];
        $backup = [];
        $totalConverted = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        if ($dryRun) {
            $this->info('DRY RUN — no files will be modified.');
        }

        foreach ($uploadDirs as $baseDir) {
            $directories = $this->getDirectoriesRecursive($baseDir);

            foreach ($directories as $dir) {
                $files = collect(File::files($dir))
                    ->filter(fn($f) => in_array(strtolower($f->getExtension()), $extensions))
                    ->filter(fn($f) => !str_ends_with(strtolower($f->getFilename()), '.webp'))
                    ->values();

                if ($files->isEmpty()) {
                    continue;
                }

                $relativeDir = str_replace($publicDir, '', $dir);
                $this->info("Processing: {$relativeDir} ({$files->count()} images)");

                foreach ($files as $file) {
                    $originalPath = str_replace('\\', '/', $file->getPathname());
                    $webpPath = str_replace('\\', '/', pathinfo($originalPath, PATHINFO_DIRNAME))
                        . '/' . pathinfo($originalPath, PATHINFO_FILENAME) . '.webp';

                    if (!$force && file_exists($webpPath)) {
                        $totalSkipped++;
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("  Would convert: {$file->getFilename()}");
                        $totalConverted++;
                        continue;
                    }

                    try {
                        $manager = ImageManager::gd();
                        $image = $manager->read(str_replace('\\', '/', $file->getPathname()));

                        $originalWidth = $image->width();
                        if ($this->maxWidth > 0 && $originalWidth > $this->maxWidth) {
                            $image->resize(width: $this->maxWidth);
                        }

                        file_put_contents($webpPath, (string) $image->toWebp($this->webpQuality));

                        $backup[] = [
                            'original' => $originalPath,
                            'webp' => $webpPath,
                        ];

                        $totalConverted++;
                        $this->line("  ✓ {$file->getFilename()} → " . basename($webpPath));
                    } catch (\Exception $e) {
                        $totalErrors++;
                        $this->error("  ✗ {$file->getFilename()}: {$e->getMessage()}");
                    }
                }
            }
        }

        if (!$dryRun && !empty($backup)) {
            $backupPath = storage_path('app/webp_backup_' . date('Y-m-d_His') . '.json');
            file_put_contents($backupPath, json_encode($backup, JSON_PRETTY_PRINT));
            $this->info("Backup saved: {$backupPath}");
        }

        $this->newLine();
        $this->info("Complete: {$totalConverted} converted, {$totalSkipped} skipped, {$totalErrors} errors");

        if (!$dryRun && $totalConverted > 0) {
            $this->info("WebP files saved alongside originals.");
        }

        return $totalErrors > 0 ? 1 : 0;
    }

    protected function rollback(): int
    {
        $backupFiles = collect(File::files(storage_path('app')))
            ->filter(fn($f) => str_starts_with($f->getFilename(), 'webp_backup_'))
            ->sortByDesc(fn($f) => $f->getFilename())
            ->values();

        if ($backupFiles->isEmpty()) {
            $this->error('No backup files found.');
            return 1;
        }

        $latestBackup = $backupFiles->first();
        $backup = json_decode(file_get_contents($latestBackup->getPathname()), true);

        if (!$backup || !is_array($backup)) {
            $this->error('Invalid backup file.');
            return 1;
        }

        $this->info("Rolling back using: {$latestBackup->getFilename()}");
        $this->info("Found " . count($backup) . " files to rollback.");

        $deleted = 0;
        foreach ($backup as $entry) {
            if (isset($entry['webp']) && file_exists($entry['webp'])) {
                @unlink($entry['webp']);
                $deleted++;
                $this->line("  ✓ Deleted: " . basename($entry['webp']));
            }
        }

        @unlink($latestBackup->getPathname());

        $this->info("Rollback complete: {$deleted} WebP files removed.");
        return 0;
    }

    protected function getDirectoriesRecursive(string $path): array
    {
        $dirs = [$path];
        $subdirs = File::directories($path);

        foreach ($subdirs as $subdir) {
            $subdirStr = str_replace('\\', '/', $subdir);
            if (str_contains($subdirStr, '_variants')) {
                continue;
            }
            $dirs = array_merge($dirs, $this->getDirectoriesRecursive($subdir));
        }

        return $dirs;
    }
}
