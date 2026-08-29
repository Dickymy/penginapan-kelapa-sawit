<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--dry-run : Only show what would be done without modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing images in storage/app/public to WebP format and create missing variants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting existing image optimization...');
        
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();
        
        $imageFiles = array_filter($allFiles, function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
        });

        if (empty($imageFiles)) {
            $this->info('No unoptimized JPG/PNG images found in storage/app/public.');
            return;
        }

        $driver = config('image.driver', 'gd');
        if ($driver === 'imagick' && extension_loaded('imagick')) {
            $manager = new ImageManager(new ImagickDriver());
        } else {
            $manager = new ImageManager(new GdDriver());
        }

        $isDryRun = $this->option('dry-run');
        $optimizedCount = 0;
        $totalSavedBytes = 0;

        foreach ($imageFiles as $filePath) {
            // Ignore variants that are already named like _thumb, _medium, _large, _original
            if (preg_match('/_(thumb|medium|large|original)\.(jpg|jpeg|png)$/i', $filePath)) {
                continue;
            }

            $originalPath = $disk->path($filePath);
            $originalSize = filesize($originalPath);
            
            $pathInfo = pathinfo($filePath);
            $directory = $pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'] . '/';
            $filename = $pathInfo['filename'];
            
            // Output path
            $webpPath = $directory . $filename . '.webp';
            
            // Skip if WebP version already exists
            if ($disk->exists($webpPath)) {
                continue;
            }

            $this->line("Processing: {$filePath} (" . round($originalSize / 1024, 2) . " KB)");

            if ($isDryRun) {
                $this->info("  -> Would convert to WebP and create variants");
                continue;
            }

            try {
                $image = $manager->read($originalPath);
                $originalWidth = $image->width();
                $originalHeight = $image->height();
                
                // Create variants
                $variants = config('image.variants', [
                    'thumb' => ['width' => 480, 'height' => 360, 'quality' => 78],
                    'medium' => ['width' => 960, 'height' => 720, 'quality' => 82],
                    'large' => ['width' => 1920, 'height' => 1440, 'quality' => 82],
                ]);

                foreach ($variants as $variantName => $config) {
                    $variantImage = clone $image;
                    
                    $maxW = $config['width'];
                    $maxH = $config['height'];
                    $quality = $config['quality'] ?? 82;

                    if ($originalWidth > $maxW || $originalHeight > $maxH) {
                        $variantImage->scaleDown(width: $maxW, height: $maxH);
                    }

                    $encoded = $variantImage->toWebp($quality)->toString();
                    $variantPath = $directory . $filename . '_' . $variantName . '.webp';
                    $disk->put($variantPath, $encoded);
                }
                
                // Create original WebP version (scaled down if necessary)
                $maxWidth = config('image.max_width', 2560);
                $maxHeight = config('image.max_height', 1920);
                $fullQuality = config('image.full_quality', 82);

                if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                    $image->scaleDown(width: $maxWidth, height: $maxHeight);
                }

                $fullEncoded = $image->toWebp($fullQuality)->toString();
                $disk->put($webpPath, $fullEncoded);
                
                $newSize = $disk->size($webpPath);
                $savedBytes = $originalSize - $newSize;
                $totalSavedBytes += $savedBytes;
                
                $savedPercent = round(($savedBytes / $originalSize) * 100, 2);
                
                // Rename original file to mark it as old so we don't process it again next time
                $disk->move($filePath, $directory . $filename . '.old_' . $pathInfo['extension']);
                
                $this->info("  -> Optimized to WebP + created variants (Saved {$savedPercent}%)");
                $optimizedCount++;

            } catch (\Exception $e) {
                $this->error("  -> Failed to optimize: " . $e->getMessage());
            }
        }

        if ($isDryRun) {
            $this->info('Dry run completed.');
        } else {
            $savedMb = round($totalSavedBytes / 1024 / 1024, 2);
            $this->info("Optimization completed! Optimized {$optimizedCount} images. Total saved space on main images: {$savedMb} MB.");
            $this->info("Note: Database records might need updating if they hardcoded the .jpg/.png extension instead of using the base name.");
        }
    }
}
