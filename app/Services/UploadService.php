<?php

namespace App\Services;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class UploadService
{
    protected int $webpQuality = 90;
    protected int $maxWidth = 1600;

    /**
     * Convert an uploaded image to WebP and save to the target directory.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $targetDir Absolute path to target directory (e.g. public_path('uploads/platters'))
     * @return string The saved WebP filename
     */
    public function uploadTo(\Illuminate\Http\UploadedFile $file, string $targetDir): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['svg'])) {
            return $this->storeOriginal($file, $targetDir);
        }

        if ($extension === 'gif' && $this->isAnimatedGif($file)) {
            return $this->storeOriginal($file, $targetDir);
        }

        try {
            $manager = ImageManager::gd();
            $image = $manager->read(file_get_contents($file->getPathname()));

            $originalWidth = $image->width();
            if ($this->maxWidth > 0 && $originalWidth > $this->maxWidth) {
                $image->resize(width: $this->maxWidth);
            }

            $imageName = Str::random(25) . '.webp';
            $fullPath = rtrim($targetDir, '/') . '/' . $imageName;
            file_put_contents($fullPath, (string) $image->toWebp($this->webpQuality));

            return $imageName;
        } catch (\Exception $e) {
            return $this->storeOriginal($file, $targetDir);
        }
    }

    /**
     * Convert an uploaded image to WebP and store on a Laravel disk.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $dir Directory path on the disk (e.g. 'profile_images')
     * @param string $disk Laravel disk name (e.g. 'public')
     * @return string The saved WebP filename
     */
    public function uploadToDisk(\Illuminate\Http\UploadedFile $file, string $dir, string $disk = 'public'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['svg'])) {
            return $this->storeOriginalToDisk($file, $dir, $disk);
        }

        if ($extension === 'gif' && $this->isAnimatedGif($file)) {
            return $this->storeOriginalToDisk($file, $dir, $disk);
        }

        try {
            $manager = ImageManager::gd();
            $image = $manager->read(file_get_contents($file->getPathname()));

            $originalWidth = $image->width();
            if ($this->maxWidth > 0 && $originalWidth > $this->maxWidth) {
                $image->resize(width: $this->maxWidth);
            }

            $imageName = Str::random(25) . '.webp';
            \Illuminate\Support\Facades\Storage::disk($disk)->put($dir . '/' . $imageName, (string) $image->toWebp($this->webpQuality));

            return $imageName;
        } catch (\Exception $e) {
            return $this->storeOriginalToDisk($file, $dir, $disk);
        }
    }

    /**
     * Backward-compatible upload method for existing callers.
     * Accepts array of UploadedFile or ['key' => UploadedFile].
     *
     * @param array $images
     * @param string $dir
     * @param string $disk
     * @return array
     */
    public function upload(array $images, string $dir = 'others', string $disk = 'public'): array
    {
        $imgData = [];

        if (!\Illuminate\Support\Facades\Storage::disk($disk)->exists($dir)) {
            \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($dir, 0775, true);
        }

        foreach ($images as $key => $img) {
            if ($img instanceof \Illuminate\Http\UploadedFile) {
                $imgData[$key] = $this->uploadToDisk($img, $dir, $disk);
            }
        }

        return $imgData;
    }

    protected function storeOriginal(\Illuminate\Http\UploadedFile $file, string $targetDir): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $imageName = Str::random(25) . '.' . $extension;
        $file->move($targetDir, $imageName);
        return $imageName;
    }

    protected function storeOriginalToDisk(\Illuminate\Http\UploadedFile $file, string $dir, string $disk): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $imageName = Str::random(25) . '.' . $extension;
        \Illuminate\Support\Facades\Storage::disk($disk)->putFileAs($dir, $file, $imageName);
        return $imageName;
    }

    protected function isAnimatedGif(\Illuminate\Http\UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        return str_contains($content, 'NETSCAPE2.0') || str_contains($content, 'ANIMEXTS');
    }
}
