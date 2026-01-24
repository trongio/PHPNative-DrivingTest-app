<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Process image from multiple input sources (file, base64, native path).
     *
     * @param  string|null  $oldImagePath  Path to delete if new image is provided
     * @return string|null The stored image path, or null if no image was processed
     */
    public function processProfileImage(
        Request $request,
        ?string $oldImagePath = null,
        string $directory = 'profile-images'
    ): ?string {
        // Handle file upload (web)
        if ($request->hasFile('profile_image')) {
            return $this->storeUploadedFile(
                $request->file('profile_image'),
                $directory,
                $oldImagePath
            );
        }

        // Handle base64 image from NativePHP (mobile) - preferred method
        if ($request->filled('profile_image_base64')) {
            return $this->storeBase64Image(
                $request->input('profile_image_base64'),
                $directory,
                $oldImagePath
            );
        }

        // Handle NativePHP camera path (mobile) - fallback for legacy
        if ($request->filled('profile_image_path')) {
            return $this->storeNativePath(
                $request->input('profile_image_path'),
                $directory,
                $oldImagePath
            );
        }

        return null;
    }

    /**
     * Store an uploaded file.
     */
    public function storeUploadedFile(
        UploadedFile $file,
        string $directory = 'profile-images',
        ?string $oldImagePath = null
    ): ?string {
        $this->deleteOldImage($oldImagePath);

        return $file->store($directory, 'public');
    }

    /**
     * Store a base64-encoded image.
     *
     * Expects data URL format: data:image/jpeg;base64,/9j/4AAQ...
     */
    public function storeBase64Image(
        string $base64Data,
        string $directory = 'profile-images',
        ?string $oldImagePath = null
    ): ?string {
        if (! preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64Data, $matches)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $contents = base64_decode($matches[2]);

        if ($contents === false) {
            return null;
        }

        $this->deleteOldImage($oldImagePath);

        $filename = $directory.'/'.uniqid().'.'.$extension;
        Storage::disk('public')->put($filename, $contents);

        return $filename;
    }

    /**
     * Store an image from a native filesystem path.
     *
     * Used for NativePHP camera captures.
     */
    public function storeNativePath(
        string $nativePath,
        string $directory = 'profile-images',
        ?string $oldImagePath = null
    ): ?string {
        if (! file_exists($nativePath)) {
            return null;
        }

        $contents = file_get_contents($nativePath);
        if ($contents === false) {
            return null;
        }

        $this->deleteOldImage($oldImagePath);

        $extension = pathinfo($nativePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $directory.'/'.uniqid().'.'.$extension;
        Storage::disk('public')->put($filename, $contents);

        return $filename;
    }

    /**
     * Delete an old image from storage.
     */
    public function deleteOldImage(?string $imagePath): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
