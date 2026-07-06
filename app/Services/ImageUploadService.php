<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE_BYTES = 2 * 1024 * 1024; // 2MB

    /**
     * Upload an image to public storage with random filename.
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        $this->validate($file);

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::random(40) . '.' . $extension;
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    /**
     * Delete an image from public storage.
     */
    public function delete(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Validate an uploaded file.
     *
     * @throws \InvalidArgumentException
     */
    private function validate(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException(
                'Format file tidak didukung. Gunakan JPEG, PNG, atau WebP.'
            );
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new \InvalidArgumentException(
                'Ukuran file terlalu besar. Maksimum 2MB.'
            );
        }
    }
}
