<?php

namespace App\Services;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UploadService
{
    protected array $variantWidths = [400, 800, 1200];
    protected int $webpQuality = 80;
    protected int $jpegQuality = 80;

    /**
     * Uploads an array of images to the specified directory on the specified disk.
     * Will create the directory if it does not exist.
     * Generates optimized WebP + JPEG variants for responsive images.
     *
     * @param array $images
     * @param string $dir
     * @param string $disk The disk to store the images on. Defaults to 'public'.
     * @param int $maxWidth Maximum width to resize to (0 = no resize, default 1200).
     * @return array An array of the uploaded images file names.
     */
    public function upload(array $images, $dir = 'others', $disk = 'public', int $maxWidth = 1200)
    {
        $imgData = [];

        if (!Storage::disk($disk)->exists($dir)) {
            Storage::disk($disk)->makeDirectory($dir, 0775, true);
        }

        foreach ($images as $key => $img) {
            $extension = strtolower($img->getClientOriginalExtension());

            // Skip GIF and SVG — no optimization
            if (in_array($extension, ['gif', 'svg'])) {
                $image_name = Str::random(25) . $img->hashName();
                Storage::disk($disk)->put($dir . $image_name, file_get_contents($img->getPathname()));
                $imgData[$key] = $image_name;
                continue;
            }

            $manager = ImageManager::gd();
            $image = $manager->read($img);

            // Resize large images (preserve aspect ratio, never upscale)
            $originalWidth = $image->width();
            if ($maxWidth > 0 && $originalWidth > $maxWidth) {
                $image->resize(width: $maxWidth);
            }

            // Encode image in its original format with quality optimization
            switch ($extension) {
                case 'png':
                    $imagedata = (string) $image->toPng();
                    break;
                case 'webp':
                    $imagedata = (string) $image->toWebp($this->webpQuality);
                    break;
                case 'bmp':
                    $imagedata = (string) $image->toBmp();
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    $imagedata = (string) $image->toJpeg($this->jpegQuality);
                    break;
            }

            $image_name = Str::random(25) . $img->hashName();
            Storage::disk($disk)->put($dir . $image_name, $imagedata);

            // Generate responsive variants (WebP + JPEG at 400w, 800w, 1200w)
            $this->generateVariants($image, $originalWidth, $dir, $image_name);

            $image = null;
            $imgData[$key] = $image_name;
        }

        return $imgData;
    }

    /**
     * Generate responsive width variants for an uploaded image.
     * Stores in public/uploads/_variants/{dir}/ as {name}_{width}w.webp and {name}_{width}w.jpg
     */
    protected function generateVariants($image, int $originalWidth, string $dir, string $imageName): void
    {
        $baseName = pathinfo($imageName, PATHINFO_FILENAME);

        // Normalize dir for filesystem paths
        $dirNormalized = str_replace('\\', '/', $dir);
        $publicBase = str_replace('\\', '/', public_path(''));
        $variantDir = $publicBase . 'uploads/_variants/' . trim($dirNormalized, '/');

        File::ensureDirectoryExists($variantDir);

        foreach ($this->variantWidths as $width) {
            if ($originalWidth <= $width) {
                $resized = $image;
            } else {
                $resized = (clone $image)->resize(width: $width);
            }

            // WebP variant
            $webpPath = $variantDir . '/' . $baseName . '_' . $width . 'w.webp';
            file_put_contents($webpPath, (string) $resized->toWebp($this->webpQuality));

            // JPEG variant
            $jpgPath = $variantDir . '/' . $baseName . '_' . $width . 'w.jpg';
            file_put_contents($jpgPath, (string) $resized->toJpeg($this->jpegQuality));
        }
    }
}
