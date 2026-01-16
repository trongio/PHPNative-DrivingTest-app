<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--force : Overwrite existing optimized images}';

    protected $description = 'Convert images to WebP format for faster loading';

    private int $converted = 0;

    private int $skipped = 0;

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('GD extension is required for image processing');

            return self::FAILURE;
        }

        $directories = [
            'ticket_images' => 50,
            'ticket_images_custom' => 50,
        ];

        $force = $this->option('force');

        foreach ($directories as $dir => $quality) {
            $sourcePath = public_path("images/{$dir}");

            if (! File::isDirectory($sourcePath)) {
                $this->warn("Directory not found: {$sourcePath}");

                continue;
            }

            $this->info("Processing {$dir}...");

            $webpPath = public_path("images/{$dir}_webp");
            if (! File::isDirectory($webpPath)) {
                File::makeDirectory($webpPath, 0755, true);
            }

            $files = File::files($sourcePath);
            $bar = $this->output->createProgressBar(count($files));
            $bar->start();

            foreach ($files as $file) {
                $this->processImage($file->getPathname(), $file->getFilename(), $webpPath, $quality, $force);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->newLine();
        $this->info('Optimization complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['WebP conversions', $this->converted],
                ['Skipped (already exists)', $this->skipped],
            ]
        );

        return self::SUCCESS;
    }

    private function processImage(string $sourcePath, string $filename, string $webpPath, int $quality, bool $force): void
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return;
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $webpFile = "{$webpPath}/{$baseName}.webp";

        if (! $force && File::exists($webpFile)) {
            $this->skipped++;

            return;
        }

        $image = $this->loadImage($sourcePath, $extension);
        if ($image) {
            imagewebp($image, $webpFile, $quality);
            imagedestroy($image);
            $this->converted++;
        }
    }

    private function loadImage(string $path, string $extension): ?\GdImage
    {
        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'gif' => @imagecreatefromgif($path),
            default => null,
        };

        if ($image === null || $image === false) {
            return null;
        }

        // Convert palette images to true color for WebP compatibility
        if (! imageistruecolor($image)) {
            $width = imagesx($image);
            $height = imagesy($image);
            $trueColor = imagecreatetruecolor($width, $height);

            imagealphablending($trueColor, false);
            imagesavealpha($trueColor, true);
            $transparent = imagecolorallocatealpha($trueColor, 0, 0, 0, 127);
            imagefill($trueColor, 0, 0, $transparent);

            imagecopy($trueColor, $image, 0, 0, 0, 0, $width, $height);
            imagedestroy($image);

            return $trueColor;
        }

        return $image;
    }
}
