<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Support\Facades\File;

class OptimizeStaticImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-static {--dry-run : Only show what would be done without modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize static images in public/images to WebP format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting static image optimization...');
        
        $publicImagesDir = public_path('images');
        if (!File::exists($publicImagesDir)) {
            $this->error('Directory public/images not found.');
            return;
        }

        $files = File::allFiles($publicImagesDir);
        $imageFiles = array_filter($files, function ($file) {
            return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png']);
        });

        if (empty($imageFiles)) {
            $this->info('No unoptimized JPG/PNG images found in public/images.');
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

        foreach ($imageFiles as $file) {
            $originalPath = $file->getRealPath();
            $originalSize = filesize($originalPath);
            $filename = $file->getFilenameWithoutExtension();
            $directory = $file->getPath();
            
            // Output path
            $webpPath = $directory . '/' . $filename . '.webp';
            
            // Skip if WebP version already exists
            if (File::exists($webpPath)) {
                continue;
            }

            $this->line("Processing: {$file->getFilename()} (" . round($originalSize / 1024, 2) . " KB)");

            if ($isDryRun) {
                $this->info("  -> Would convert to WebP");
                continue;
            }

            try {
                $image = $manager->read($originalPath);
                
                // Scale down if too large (hero image might be massive)
                $maxWidth = 1920;
                $maxHeight = 1080;
                if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                    $image->scaleDown(width: $maxWidth, height: $maxHeight);
                }

                $encoded = $image->toWebp(80)->toString();
                File::put($webpPath, $encoded);
                
                $newSize = filesize($webpPath);
                $savedBytes = $originalSize - $newSize;
                $totalSavedBytes += $savedBytes;
                
                $savedPercent = round(($savedBytes / $originalSize) * 100, 2);
                
                $this->info("  -> Optimized to WebP: " . round($newSize / 1024, 2) . " KB (Saved {$savedPercent}%)");
                $optimizedCount++;

            } catch (\Exception $e) {
                $this->error("  -> Failed to optimize: " . $e->getMessage());
            }
        }

        if ($isDryRun) {
            $this->info('Dry run completed.');
        } else {
            $savedMb = round($totalSavedBytes / 1024 / 1024, 2);
            $this->info("Optimization completed! Optimized {$optimizedCount} images. Total saved space: {$savedMb} MB.");
            $this->info("Don't forget to update your Blade files to point to the new .webp extensions.");
        }
    }
}
