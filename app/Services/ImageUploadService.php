<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    private ImageManager $manager;

    public function __construct()
    {
        $driver = config('image.driver', 'gd');

        if ($driver === 'imagick' && extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
        } else {
            $this->manager = new ImageManager(new GdDriver());
        }
    }

    /**
     * Upload, validate, optimize and create variants of an image.
     *
     * Returns an array with paths to all created variants:
     * ['original' => '...', 'large' => '...', 'medium' => '...', 'thumb' => '...']
     *
     * For backward compatibility, calling code can use the 'large' path as the main path.
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        $this->validate($file);
        $result = $this->processAndStore($file, $directory);

        return $result['large'] ?? $result['original'];
    }

    /**
     * Upload with all variants returned.
     *
     * @return array{original: string, large: string, medium: string, thumb: string}
     */
    public function uploadWithVariants(UploadedFile $file, string $directory): array
    {
        $this->validate($file);

        return $this->processAndStore($file, $directory);
    }

    /**
     * Delete an image and all its variants from storage.
     */
    public function delete(string $path): void
    {
        $disk = Storage::disk('public');

        // Delete the main file
        if ($disk->exists($path)) {
            $disk->delete($path);
        }

        // Try to delete variants based on naming convention
        $pathInfo = pathinfo($path);
        $baseName = $pathInfo['filename'];
        $dir = $pathInfo['dirname'];

        foreach (array_keys(config('image.variants', [])) as $variant) {
            // Check both webp and original extension variants
            foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
                $variantPath = $dir . '/' . $baseName . '_' . $variant . '.' . $ext;
                if ($disk->exists($variantPath)) {
                    $disk->delete($variantPath);
                }
            }
        }

        // Also try the _original suffix
        foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
            $originalPath = $dir . '/' . $baseName . '_original.' . $ext;
            if ($disk->exists($originalPath)) {
                $disk->delete($originalPath);
            }
        }
    }

    /**
     * Delete all variant files for a given base path.
     */
    public function deleteVariants(string $basePath): void
    {
        $this->delete($basePath);
    }

    /**
     * Validate an uploaded file.
     *
     * @throws \InvalidArgumentException
     */
    private function validate(UploadedFile $file): void
    {
        $maxBytes = config('image.upload_max_mb', 15) * 1024 * 1024;
        $allowedMimes = config('image.allowed_mimes', ['image/jpeg', 'image/png', 'image/webp']);

        // Check actual MIME type (not just client-provided extension)
        $actualMime = $file->getMimeType();
        if (!in_array($actualMime, $allowedMimes)) {
            throw new \InvalidArgumentException(
                'Format gambar tidak didukung. Gunakan JPEG, PNG, atau WebP.'
            );
        }

        if ($file->getSize() > $maxBytes) {
            $maxMb = config('image.upload_max_mb', 15);
            throw new \InvalidArgumentException(
                "Ukuran file terlalu besar. Maksimum {$maxMb} MB."
            );
        }

        // Verify it's actually a valid image
        if (!$this->isValidImage($file)) {
            throw new \InvalidArgumentException(
                'File rusak atau bukan gambar yang valid.'
            );
        }
    }

    /**
     * Check if a file is actually a valid image (not just by extension).
     */
    private function isValidImage(UploadedFile $file): bool
    {
        try {
            $imageInfo = @getimagesize($file->getPathname());

            return $imageInfo !== false && $imageInfo[0] > 0 && $imageInfo[1] > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Process image: fix orientation, resize, compress, create variants.
     *
     * @return array<string, string> Map of variant name to storage path
     */
    private function processAndStore(UploadedFile $file, string $directory): array
    {
        $baseName = Str::random(40);
        $outputFormat = config('image.output_format', 'webp');
        $extension = $outputFormat === 'webp' ? 'webp' : 'jpg';
        $disk = Storage::disk('public');
        $paths = [];

        try {
            // Read (Intervention Image v3 auto-orients based on EXIF by default)
            $image = $this->manager->read($file->getPathname());

            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Create variants (from smallest to largest to reuse downscaled versions)
            $variants = config('image.variants', [
                'thumb' => ['width' => 480, 'height' => 360, 'quality' => 78],
                'medium' => ['width' => 960, 'height' => 720, 'quality' => 82],
                'large' => ['width' => 1920, 'height' => 1440, 'quality' => 82],
            ]);

            foreach ($variants as $variantName => $config) {
                $variantImage = $this->manager->read($file->getPathname());

                $maxW = $config['width'];
                $maxH = $config['height'];
                $quality = $config['quality'] ?? 82;

                // Only downscale, never upscale
                if ($originalWidth > $maxW || $originalHeight > $maxH) {
                    $variantImage->scaleDown(width: $maxW, height: $maxH);
                }

                // Encode
                $encoded = $this->encode($variantImage, $outputFormat, $quality);

                // Store
                $variantFilename = $baseName . '_' . $variantName . '.' . $extension;
                $variantPath = $directory . '/' . $variantFilename;
                $disk->put($variantPath, $encoded);
                $paths[$variantName] = $variantPath;
            }

            // Also save a "full" version (resized to max dimensions but high quality)
            $maxWidth = config('image.max_width', 2560);
            $maxHeight = config('image.max_height', 1920);
            $fullQuality = config('image.full_quality', 82);

            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $image->scaleDown(width: $maxWidth, height: $maxHeight);
            }

            $fullEncoded = $this->encode($image, $outputFormat, $fullQuality);
            $fullFilename = $baseName . '.' . $extension;
            $fullPath = $directory . '/' . $fullFilename;
            $disk->put($fullPath, $fullEncoded);
            $paths['original'] = $fullPath;

            return $paths;
        } catch (\Throwable $e) {
            // Cleanup any files that were already created
            foreach ($paths as $path) {
                if ($disk->exists($path)) {
                    $disk->delete($path);
                }
            }

            Log::error('Image processing failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            throw new \InvalidArgumentException(
                'Foto gagal diproses. Silakan coba lagi dengan file lain.'
            );
        }
    }

    /**
     * Encode image to the specified format.
     */
    private function encode($image, string $format, int $quality): string
    {
        try {
            if ($format === 'webp') {
                return $image->toWebp($quality)->toString();
            }

            return $image->toJpeg($quality)->toString();
        } catch (\Throwable) {
            // Fallback to JPEG if WebP encoding fails
            return $image->toJpeg($quality)->toString();
        }
    }
}
