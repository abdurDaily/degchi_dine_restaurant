<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
                            {--dry-run : Show what would be done without making changes}
                            {--path= : Specific subdirectory under public/uploads to process}';

    protected $description = 'Generate optimized WebP + JPEG variants for uploaded images (400w, 800w, 1200w)';

    protected $widths = [400, 800, 1200];
    protected $webpQuality = 80;
    protected $jpegQuality = 80;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $path = $this->option('path');
        $publicDir = str_replace('\\', '/', public_path(''));
        $uploadDir = $publicDir . '/uploads';
        $variantsDir = $publicDir . '/uploads/_variants';

        if ($dryRun) {
            $this->info('DRY RUN — no files will be modified.');
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if ($path) {
            $dirs = [$uploadDir . '/' . trim($path, '/')];
        } else {
            $dirs = collect(File::directories($uploadDir))
                ->filter(fn($d) => !str_contains($d, '_variants'))
                ->map(fn($d) => str_replace('\\', '/', $d))
                ->toArray();

            // Also include storage/profile_images if it exists
            $storageProfileDir = str_replace('\\', '/', public_path('storage/profile_images'));
            if (is_dir($storageProfileDir)) {
                $dirs[] = $storageProfileDir;
            }
        }

        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($dirs as $dir) {
            $dir = str_replace('\\', '/', $dir);
            $files = collect(File::files($dir))
                ->filter(fn($f) => in_array(strtolower($f->getExtension()), $extensions))
                ->values();

            if ($files->isEmpty()) {
                continue;
            }

            // For storage files, map to uploads/_variants/ structure
            $isStorage = str_contains($dir, '/storage/');
            if ($isStorage) {
                $storageSubDir = str_replace(public_path('storage') . '/', '', $dir);
                $relativeDir = $storageSubDir; // e.g., 'profile_images'
                $variantOutputDir = $variantsDir . '/' . $storageSubDir;
            } else {
                $relativeDir = str_replace($uploadDir . '/', '', $dir);
                $variantOutputDir = $variantsDir . '/' . $relativeDir;
            }
            $this->info("Processing: {$relativeDir} ({$files->count()} images)");

            foreach ($files as $file) {
                $baseName = $file->getFilenameWithoutExtension();
                $allExist = true;

                foreach ($this->widths as $width) {
                    foreach (['webp', 'jpg'] as $format) {
                        $variantPath = $this->getVariantPath($relativeDir, $baseName, $width, $format);
                        if (!File::exists($variantPath)) {
                            $allExist = false;
                            break 2;
                        }
                    }
                }

                if ($allExist) {
                    $totalSkipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  Would process: {$file->getFilename()}");
                    $totalProcessed++;
                    continue;
                }

                try {
                    $manager = ImageManager::gd();
                    $image = $manager->read(str_replace('\\', '/', $file->getPathname()));
                    $originalWidth = $image->width();

                    foreach ($this->widths as $width) {
                        $targetWidth = ($originalWidth <= $width) ? $originalWidth : $width;

                        $resized = ($targetWidth < $originalWidth)
                            ? $image->resize(width: $targetWidth)
                            : $image;

                        $variantDir = $variantOutputDir;
                        File::ensureDirectoryExists($variantDir);

                        // WebP variant
                        $webpPath = $variantDir . '/' . $baseName . '_' . $width . 'w.webp';
                        $webpData = (string) $resized->toWebp($this->webpQuality);
                        file_put_contents($webpPath, $webpData);

                        // JPEG variant
                        $jpgPath = $variantDir . '/' . $baseName . '_' . $width . 'w.jpg';
                        $jpgData = (string) $resized->toJpeg($this->jpegQuality);
                        file_put_contents($jpgPath, $jpgData);
                    }

                    $totalProcessed++;
                    $this->line("  ✓ {$file->getFilename()} ({$originalWidth}w)");
                } catch (\Exception $e) {
                    $totalErrors++;
                    $this->error("  ✗ {$file->getFilename()}: {$e->getMessage()}");
                }
            }
        }

        $this->newLine();
        $this->info("Complete: {$totalProcessed} processed, {$totalSkipped} skipped, {$totalErrors} errors");

        if (!$dryRun && $totalProcessed > 0) {
            $this->info("Variants saved in: public/uploads/_variants/");
        }

        return $totalErrors > 0 ? 1 : 0;
    }

    protected function getVariantPath(string $relativeDir, string $baseName, int $width, string $format): string
    {
        $ext = ($format === 'webp') ? 'webp' : 'jpg';
        $variantsDir = str_replace('\\', '/', public_path('')) . '/uploads/_variants';
        return $variantsDir . '/' . $relativeDir . '/' . $baseName . '_' . $width . 'w.' . $ext;
    }
}
