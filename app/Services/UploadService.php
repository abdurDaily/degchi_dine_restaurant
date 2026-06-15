<?php

namespace App\Services;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\AutoEncoder;

class UploadService
{
    /**
     * Uploads an array of images to the specified directory on the specified disk.
     * Will create the directory if it does not exist.
     *
     * @param array $images
     * @param string $dir
     * @param string $disk The disk to store the images on. Defaults to 'public'.
     * @return array An array of the uploaded images file names.
     */
    public function upload(array $images, $dir = 'others', $disk = 'public')
    {
        $imgData = [];

        // Ensure directory exists
        if (!Storage::disk($disk)->exists($dir))
        {
            Storage::disk($disk)->makeDirectory($dir, 0775, true);
        }

        foreach ($images as $key => $img)
        {

            $manager = ImageManager::gd();
            $image = $manager
                ->read($img);
            // ->scale(height: 200);

            // Get original extension
            $extension = $img->getClientOriginalExtension();

            // Encode image in its original format
            switch (strtolower($extension))
            {
                case 'png':
                    $imagedata = (string) $image->toPng();
                    break;
                case 'gif': // Keep transparency for GIFs
                    $imagedata = (string) $image->toGif();
                    break;
                case 'webp':
                    $imagedata = (string) $image->toWebp();
                    break;
                case 'bmp':
                    $imagedata = (string) $image->toBmp();
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    $imagedata = (string) $image->toJpeg(); // Default to JPEG (no transparency)
                    break;
            }


            $image_name =  Str::random(25) . $img->hashName();
            Storage::disk($disk)->put($dir . $image_name, $imagedata);
            $image = null;
            $imgData[$key] = $image_name;
        }

        return $imgData;
    }
}
