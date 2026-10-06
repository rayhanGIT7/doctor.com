<?php

namespace App\Core;

/**
 * Image upload helper. The file type is detected from the file content
 * (not from the name the browser sends), and a random file name is used.
 */
class Upload
{
    private const MAX_SIZE = 2 * 1024 * 1024; // 2 MB

    private const TYPES = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    public static function hasFile(string $field): bool
    {
        return isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    // Returns an error message, or null when the image is fine
    public static function validateImage(string $field): ?string
    {
        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Image upload failed. Please try again.';
        }
        if ($file['size'] > self::MAX_SIZE) {
            return 'Image must be smaller than 2 MB.';
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false || !isset(self::TYPES[$info[2]])) {
            return 'Image must be a JPG, PNG or WEBP file.';
        }

        return null;
    }

    // Saves the image to public/uploads/{folder}/ and returns the path relative to public/uploads
    public static function saveImage(string $field, string $folder): string
    {
        $file      = $_FILES[$field];
        $extension = self::TYPES[getimagesize($file['tmp_name'])[2]];
        $name      = bin2hex(random_bytes(16)) . '.' . $extension;
        $directory = BASE_PATH . '/public/uploads/' . $folder;

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $directory . '/' . $name);

        return $folder . '/' . $name;
    }

    public static function delete(?string $path): void
    {
        if ($path && is_file(BASE_PATH . '/public/uploads/' . $path)) {
            unlink(BASE_PATH . '/public/uploads/' . $path);
        }
    }
}
