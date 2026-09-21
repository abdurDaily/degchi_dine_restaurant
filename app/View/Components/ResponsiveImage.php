<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public string $src;
    public string $alt;
    public ?string $width;
    public ?string $height;
    public ?string $class;
    public ?string $loading;
    public ?string $decoding;
    public ?string $fetchpriority;
    public ?string $onerror;
    public ?string $sizes;

    public function __construct(
        string $src,
        string $alt = '',
        ?string $width = null,
        ?string $height = null,
        ?string $class = null,
        ?string $loading = null,
        ?string $decoding = null,
        ?string $fetchpriority = null,
        ?string $onerror = null,
        ?string $sizes = null,
    ) {
        $this->src = $src;
        $this->alt = $alt;
        $this->width = $width;
        $this->height = $height;
        $this->class = $class;
        $this->loading = $loading;
        $this->decoding = $decoding;
        $this->fetchpriority = $fetchpriority;
        $this->onerror = $onerror;
        $this->sizes = $sizes;
    }

    public function render(): string
    {
        return view('components.responsive-image');
    }

    public function hasVariants(): bool
    {
        return $this->getVariantPath(400, 'webp') !== null;
    }

    public function getVariantUrl(int $width, string $format): ?string
    {
        $path = $this->getVariantPath($width, $format);
        return $path ? asset($path) : null;
    }

    public function getVariantPath(int $width, string $format): ?string
    {
        $src = $this->src;

        // Skip external URLs
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            return null;
        }

        // Strip asset() wrapper if present
        $src = str_replace(['/storage/', '/uploads/'], ['uploads/', 'uploads/'], $src);
        $src = ltrim($src, '/');

        // If it starts with 'uploads/', check variants
        if (!str_starts_with($src, 'uploads/')) {
            return null;
        }

        // Extract filename and directory
        $pathInfo = pathinfo($src);
        $dirname = $pathInfo['dirname']; // e.g., 'uploads/menus/variations'
        $basename = $pathInfo['filename']; // e.g., '1780748896_0'
        $ext = ($format === 'webp') ? 'webp' : 'jpg';

        $variantRelative = $dirname . '/_variants/' . $basename . '_' . $width . 'w.' . $ext;
        $variantAbsolute = public_path($variantRelative);

        if (file_exists($variantAbsolute)) {
            return $variantRelative;
        }

        return null;
    }

    public function getOriginalAbsolute(): ?string
    {
        $src = $this->src;
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            return null;
        }
        return public_path(ltrim($src, '/'));
    }
}
