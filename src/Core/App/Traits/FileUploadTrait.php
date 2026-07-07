<?php

namespace App\Traits;

trait FileUploadTrait
{
    /**
     * Handle single file upload and return JSON encoded metadata.
     *
     * @param string $fieldName  The name of the file input field (default: 'image')
     * @param string|null $alt   Optional alt text for the image
     * @param string $uploadPath Relative path to the upload directory (default: 'img/showroom')
     * @return string JSON encoded array with file metadata
     */
    public function uploadFile(string $fieldName = 'image', ?string $alt = null, string $uploadPath = 'img/showroom'): string
    {
        // Ensure file is present
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return json_encode([]);
        }

        $fileInfo      = $_FILES[$fieldName];
        $fileTmpPath   = $fileInfo['tmp_name'];
        $originalName  = $fileInfo['name'];
        $fileType      = $fileInfo['type'];
        $fileSize      = $fileInfo['size'];
        $extension     = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Validate extension (optional)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            return json_encode([]);
        }

        // Sanitize filename
        $baseName    = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName    = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $newFileName = $safeName . '.' . $extension;

        // Set upload directory
        $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . trim($uploadPath, '/') . '/';
        // permission file folder
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $newFileName;

        // Move file
        if (!is_uploaded_file($fileTmpPath) || !move_uploaded_file($fileTmpPath, $destPath)) {
            return json_encode([]);
        }

        // Build image data
        $imageData = [
            [
                'alt'           => $alt ?? $safeName,
                'objectURL'     => '/' . trim($uploadPath, '/') . '/' . $newFileName,
                'full_path'     => $originalName,
                'type'          => $fileType,
                'tmp_name'      => $fileTmpPath,
                'size'          => $fileSize,
                'extension'     => $extension,
                'new_name'      => $newFileName,
                'absolute_path' => $destPath
            ]
        ];

        return json_encode($imageData);
    }
}
