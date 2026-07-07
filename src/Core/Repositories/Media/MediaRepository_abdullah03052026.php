<?php

declare(strict_types=1);

namespace App\Core\Repositories\Media;

use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Models\Media\Media;
use App\Core\Repositories\Base\BaseRepository;  
use Exception;
use PDO;

class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
    private const BANNER_TARGET_WIDTH = 1920;

    /** Maximum upload size (MB) for `upload()` when callers rely on the default. */
    private const UPLOAD_MAX_FILE_MB = 25;

    public function __construct(PDO $db)
    {
        parent::__construct($db, 'media', Media::class);
    }

    /**
     * Get all media content with pagination and filtering
     */
    public function getAll(
        ?int $media_id = null,
        ?string $file = null,
        ?string $type = null,
        ?string $meta = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($media_id !== null) {
            $query->where('media_id', '=', $media_id);
        }

        if ($file !== null) {
            $query->where('file', '=', $file);
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        if ($meta !== null) {
            $query->where('meta', '=', $meta);
        }

        $query->orderBy('media_id', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    /**
     * Get media content by ID
     */
    public function get(int $mediaId): ?Media
    {
        $query = $this->model
            ->where('media_id', '=', $mediaId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    public function getImageMasonryGallery(array $param) {
        $query = $this->model;
        $query->where('type', '=', 'image');
        $query->orderBy('media_id', 'DESC');

        if(isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }
        if(isset($param['fields']) && is_array($param['fields'])) {
            $query->select($param['fields']);
        }
        
        $result = $query->findAll();
        
        return $result;
    }

    public function uploadFile(array $data, ?int $folder_id = null, ?string $folder_path = null): ?array
    {
        //get the file from the request
        if (!isset($data['files'])) {
            return null;
        }

        $files = $data['files'];
       

        //validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $filesData = [];
        $filesResponseData = [];
        $error = [];

        foreach($files as $file){
            $e = [];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $e['file_error'] = 'File upload failed';
            }
            if (!in_array($file['type'], $allowedTypes)) {
                $e['file_type'] = 'File type not allowed';
            }
    
            //validate file size
            if ($file['size'] > 1024 * 1024 * 5) {
                $e['file_size'] = 'File size is too large';
            }
    
            //create upload directory if it doesn't exist
            $uploadDirName = $data['upload_dir'] ?? 'uploads/media/' . date('Y/m');
            $uploadDir = ROOT_DIR . DS . 'public' . DS . $uploadDirName;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            //generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = isset($file['name']) ? str_replace(' ', '_', $file['name']) : uniqid() . '.' . $extension;
            $filename = str_replace("'", '', $filename);
            $filepath = $uploadDir . '/' . $filename;
    
            //save the file to a particular folder
            if (count($error) || !move_uploaded_file($file['tmp_name'], $filepath)) {
                $e['file_save'] = 'Failed to save file';
            }
    
            //prepare media data for media table to be inserted
            if(empty($e)){
                $filesData[] = [
                    'file' => json_encode([
                        'path' => DS . $uploadDirName . DS . $filename,
                        'name' => $filename,
                        'type' => $file['type'],
                        'size' => $file['size'],
                        'mime_type' => $file['type'],
                        'objectURL' => DS . $uploadDirName . DS . $filename,
                    ]),
                    'type' => $file['type'],
                    'folder_id' => $folder_id,
                    'meta' => json_encode([
                        'original_name' => $file['name'],
                        'size' => $file['size'],
                        'mime_type' => $file['type']
                    ])
                ];
                $filesResponseData[] = [
                    'name' => $file['name'],
                    'image' => DS . $uploadDirName . DS . $filename,
                    'description' => $file['description'] ?? "",
                    'size' => $file['size'],
                    'type' => $file['type'],
                    'objectURL' => DS . $uploadDirName . DS . $filename,
                    'file' => $file,
                    'status' => ['name' => 'Uploaded', 'severity' => 'success']
                ];
            }else{
                $error[$file['name']] = $e;
            }
        }

        

        //insert the media data into the media table
        try{
            if(count($filesData)){
                $this->model->insert($filesData);
            }
        }catch(\PDOException $e){
            return null;
        }

        return ['files' => $filesResponseData, 'error' => $error];
    }

    /**
     * Upload files, optionally normalize to WebP, and optionally fit images to a target canvas.
     *
     * Flow per file: (1) validate and save to disk → (2) if no target W/H, convert non-WebP images to WebP at same size
     * → (3) if `$size['width']` and `$size['height']` are set, run contain-fit + white letterbox + WebP via
     * `processImageWithSize()` (skips step 2 to avoid double conversion) → (4) persist media rows and attach `media_id`s.
     *
     * GIF, PDF, Zip, and Office documents use `uploadOtherFiles()` after save (GIF animation preserved). Zip and
     * Office Open XML (docx/xlsx/pptx) may be recompressed in-place when smaller. Raster images are converted to WebP
     * where applicable. Maximum upload size defaults to 5MB (`UPLOAD_MAX_FILE_MB`).
     */
    public function upload(array $data, ?array $size = [], ?string $folder_path = null, ?int $folder_id = null, $is_banner = false, int $file_max_size = 25): ?array
    {
        //get the file from the request
        if (!isset($data['files'])) {
            return null;
        }

        $f = $data['files'];
        $targetWidth = null;
        $targetHeight = null;
        
        // Extract size parameters with defaults
        if(isset($size['width']) && isset($size['height'])){
            $targetWidth = $size['width'];
            $targetHeight = $size['height'];
        }
        // .rfa input type file
        //validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg', 'application/pdf', 'application/zip', 'application/max', 'application/dwg', 'application/skp', 'application/rfa','application/octet-stream','image/vnd.dwg','image/vnd.dxf', 'application/x-dwg','application/x-dxf','application/x-rfa'];

        $filesData = [];
        $filesResponseData = [];
        $error = [];
        $path = [];

        foreach($f as $key => $file){

            $e = [];
            if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
                $e['file_error'] = 'File upload failed.';
            }
            if (isset($file['type']) && !in_array($file['type'], $allowedTypes)) {
                $e['file_type'] = 'File type not allowed.';
            }
    
            //validate file size
            if (isset($file['size']) && $file['size'] > 1024 * 1024 * $file_max_size) {
                $e['file_size'] = 'File size is too large.';
            }
    
            //create upload directory if it doesn't exist
            $uploadDirName = $data['upload_dir'] ?? 'media/uploads/' . date('Y/m');
            // $uploadDirName = $data['upload_dir'] ?? 'media/sites';
            $uploadDir = ROOT_DIR . DS . 'public' . DS . $uploadDirName;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            //generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = isset($file['name']) ? str_replace(' ', '_', $file['name']) : uniqid(). $extension;
            $filename = str_replace("'", '', $filename);
            $filepath = $uploadDir . '/' . $filename;
    
            //save the file to a particular folder
            try {
                if (count($e) || !move_uploaded_file($file['tmp_name'], $filepath)) {
                    $e['file_save'] = 'Failed to save file.';
                }
            } catch (\Throwable $th) {
                $e['file_save'] = $th->getMessage();
            }

            // add zip file type application/zip and max, dwg, skp, rfa, zip, doc, docx, xls, xlsx, ppt, pptx
            $otherFiles = ['image/gif', 'application/pdf', 'application/zip', 'application/max', 'application/dwg', 'application/skp', 'application/rfa'];
            if (empty($e) && isset($file['type']) && in_array($file['type'], $otherFiles, true)) {
                $otherResult = $this->uploadOtherFiles($file, $filepath, $uploadDirName, $folder_id);
                if (isset($otherResult['errors'])) {
                    $error[$file['name']] = $otherResult['errors'];
                    $path[] = '';
                } else {
                    $filesData[] = $otherResult['filesData'];
                    $filesResponseData[] = $otherResult['filesResponseData'];
                    $path[] = $otherResult['path'];
                }
                continue;
            }

            


            // Raster uploads may later be resized; PDFs and non-images skip WebP/resize paths.
            $isRasterImage = in_array($file['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'], true);
            // When we will letterbox + WebP in processImageWithSize(), skip this pass (same destination format, one write).
            $willProcessWithTargetSize = $targetWidth && $targetHeight && $isRasterImage;

            // In-place convert jpg/png/gif → WebP at original dimensions (no target box requested).
            if (empty($e) && !$willProcessWithTargetSize) {
                $webpSameSize = $this->tryConvertRasterToWebpSameDimensions($filepath);
                if ($webpSameSize !== null) {
                    $filepath = $webpSameSize['filepath'];
                    $filename = $webpSameSize['filename'];
                    $file['type'] = $webpSameSize['type'];
                    $file['name'] = $webpSameSize['filename'];
                    $file['size'] = $webpSameSize['size'];
                }
            }

            // Defaults match the file on disk until optional processing runs.
            $processedImagePath = $filepath;
            $processedImageSize = $file['size'];
            $imageDimensions = null;

            // Target W/H: contain-fit inside box, white bars, WebP output (path may change to .webp).
            if (in_array($file['type'], $allowedTypes)) {
                if ($targetWidth && $targetHeight) {
                    $processedImageData = $this->processImageWithSize($filepath, $targetWidth, $targetHeight, $size, $is_banner);
                    if ($processedImageData) {
                        $processedImageSize = $processedImageData['size'];
                        $imageDimensions = $processedImageData['dimensions'];
                        $processedImagePath = $processedImageData['path'];
                        $file['type'] = 'image/webp';
                        $file['name'] = basename($processedImageData['path']);
                        $file['size'] = $processedImageSize;
                    }
                }
            }
    
            //prepare media data for media table to be inserted
            if(empty($e)){
                $filesData[] = [
                    'file' => json_encode([
                        'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                        'name' => basename($processedImagePath),
                        'type' => $file['type'],
                        'size' => $processedImageSize,
                        'mime_type' => $file['type'],
                        'objectURL' => DS . $uploadDirName . DS . basename($processedImagePath),
                        'dimensions' => $imageDimensions,
                        'target_width' => $targetWidth,
                        'target_height' => $targetHeight
                    ]),
                    'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'type' => $file['type'],
                    'folder_id' => $folder_id,
                    'meta' => json_encode([
                        'original_name' => $file['name'],
                        'size' => $processedImageSize,
                        'mime_type' => $file['type'],
                        'original_size' => $file['size'],
                        'dimensions' => $imageDimensions,
                        'target_width' => $targetWidth,
                        'target_height' => $targetHeight
                    ])
                ];
                $filesResponseData[] = [
                    'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'name' => basename($processedImagePath),
                    'image' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'description' => $file['description'] ?? "",
                    'size' => $processedImageSize,
                    'type' => $file['type'],
                    'objectURL' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'file' => $file,
                    'dimensions' => $imageDimensions,
                    'target_size' => ['width' => $targetWidth, 'height' => $targetHeight],
                    'status' => ['name' => 'Uploaded', 'severity' => 'success'],
                    'media_id' => null
                ];
            }else{
                $error[$file['name']] = $e;
            }

            $path[] = $filesData[$key]['path'] ?? '';
        }

        // Persist rows and map paths back to media_id for the API response.
        try{
            if(count($filesData)){
               // $this->model->insert($filesData);
               $this->model->upsert($filesData, ['path']);
               $mediaIds = $this->model->whereIn('path', $path)->select(['media_id', 'path'])->limit(0)->findAll();
               $mediaIdMap = array_column($mediaIds, 'media_id', 'path');
                foreach($filesResponseData as $key => $file){
                    if(isset($mediaIdMap[$file['path']])){
                        $filesResponseData[$key]['media_id'] = $mediaIdMap[$file['path']];
                    }else{
                        $filesResponseData[$key]['media_id'] = null;
                    }
                }
            //    $path = $filesData[0]['path'];
            //    $mediaId = $this->model->where('path', '=', $path)->select(['media_id'])->first();
            //    $filesResponseData[0]['media_id'] = $mediaId ? $mediaId->media_id : null;
               return ['files' => $filesResponseData, 'error' => $error];
            }
        }catch(\PDOException $e){
            return null;
        }

        return ['files' => $filesResponseData, 'error' => $error];
    }

    /**
     * Build media rows for uploads already saved by `upload()` (GIF, PDF, Zip, Office, CAD): no WebP conversion.
     * Zip and Office Open XML archives may be repacked with maximum deflate when it reduces size on disk.
     *
     * @return array{filesData: array, filesResponseData: array, path: string}|array{errors: array}
     */
    private function uploadOtherFiles(array $file, string $filepath, string $uploadDirName, ?int $folder_id): array
    {
        if (!is_file($filepath)) {
            return ['errors' => ['file_save' => 'File not found after upload']];
        }

        $mimeType = $file['type'] ?? 'application/octet-stream';
        $processedImagePath = $filepath;
        $processedImageSize = (int) filesize($filepath);
        // $optimized = $this->tryOptimizeZipBasedUpload($filepath, $mimeType);
        // $processedImageSize = $optimized !== null ? $optimized['size'] : (int) filesize($filepath);
        $imageDimensions = null;
        if (str_starts_with($mimeType, 'image/')) {
            $info = @getimagesize($filepath);
            if ($info !== false) {
                $imageDimensions = ['width' => $info[0], 'height' => $info[1]];
            }
        }

        $fileOut = $file;
        $fileOut['size'] = $processedImageSize;
        $fileOut['type'] = $mimeType;

        $basename = basename($processedImagePath);
        $relPath = DS . $uploadDirName . DS . $basename;

        $filesData = [
            'file' => json_encode([
                'path' => $relPath,
                'name' => $basename,
                'type' => $mimeType,
                'size' => $processedImageSize,
                'mime_type' => $mimeType,
                'objectURL' => $relPath,
                'dimensions' => $imageDimensions,
                'target_width' => null,
                'target_height' => null,
            ]),
            'path' => $relPath,
            'type' => $mimeType,
            'folder_id' => $folder_id,
            'meta' => json_encode([
                'original_name' => $file['name'],
                'size' => $processedImageSize,
                'mime_type' => $mimeType,
                'original_size' => $file['size'],
                'dimensions' => $imageDimensions,
                'target_width' => null,
                'target_height' => null,
            ]),
        ];

        $filesResponseData = [
            'path' => $relPath,
            'name' => $basename,
            'image' => $relPath,
            'description' => $file['description'] ?? '',
            'size' => $processedImageSize,
            'type' => $mimeType,
            'objectURL' => $relPath,
            'file' => $fileOut,
            'dimensions' => $imageDimensions,
            'target_size' => ['width' => null, 'height' => null],
            'status' => ['name' => 'Uploaded', 'severity' => 'success'],
            'media_id' => null,
        ];

        return [
            'filesData' => $filesData,
            'filesResponseData' => $filesResponseData,
            'path' => $relPath,
        ];
    }

    /**
     * Repack zip-based uploads with maximum deflate when the result is smaller (Zip, docx, xlsx, pptx).
     */
    private function tryOptimizeZipBasedUpload(string $filepath, string $mimeType): ?array
    {
        $zipFamily = [
            'application/zip',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/x-compressed',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        if (!in_array($mimeType, $zipFamily, true)) {
            return null;
        }

        return $this->tryRecompressZipFile($filepath);
    }

    /**
     * @return array{size: int}|null New size if the archive was rewritten smaller; null if skipped or unchanged.
     */
    private function tryRecompressZipFile(string $filepath): ?array
    {
        if (!class_exists(\ZipArchive::class)) {
            return null;
        }

        $src = new \ZipArchive();
        if ($src->open($filepath) !== true) {
            return null;
        }

        $tmp = $filepath . '.repack.' . uniqid('', true) . '.zip';
        $dst = new \ZipArchive();
        if ($dst->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $src->close();
            return null;
        }

        for ($i = 0; $i < $src->numFiles; $i++) {
            $stat = $src->statIndex($i);
            if ($stat === false) {
                continue;
            }
            $name = $stat['name'];
            if (str_ends_with($name, '/')) {
                $dst->addEmptyDir($name);
                continue;
            }
            $content = $src->getFromIndex($i);
            if ($content === false) {
                $src->close();
                $dst->close();
                @unlink($tmp);
                return null;
            }
            $dst->addFromString($name, $content);
            $dst->setCompressionName($name, \ZipArchive::CM_DEFLATE, 9);
        }

        $src->close();
        $dst->close();

        $newSize = is_file($tmp) ? (int) filesize($tmp) : 0;
        $oldSize = (int) filesize($filepath);
        if ($newSize > 0 && $newSize < $oldSize) {
            rename($tmp, $filepath);
            return ['size' => (int) filesize($filepath)];
        }

        @unlink($tmp);
        return null;
    }

    public function uploadFiles(array $data, array $allowedTypes, ?string $folder_path = null, ?int $folder_id = null): ?array
    {
        //get the file from the request
        if (!isset($data['files'])) {
            return null;
        }


        $filesData = [];
        $filesResponseData = [];
        $error = [];

        foreach($data['files'] as $file){
            $e = [];
            if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
                $e['file_error'] = 'File upload failed';
            }
            if (isset($file['type']) && !in_array($file['type'], $allowedTypes)) {
                $e['file_type'] = 'File type not allowed';
            }
    
            //validate file size
            if (isset($file['size']) && $file['size'] > 1024 * 1024 * 5) {
                $e['file_size'] = 'File size is too large';
            }
            if(isset($file['type']) && !in_array($file['type'], $allowedTypes)){
                $e['file_format'] = 'File format not allowed';
            }
    
            //create upload directory if it doesn't exist
            $uploadDirName = $folder_path ?? 'media/uploads/' . date('Y/m');
            $uploadDir = ROOT_DIR . DS . 'public' . DS . $uploadDirName;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            //generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = isset($file['name']) ? str_replace(' ', '_', $file['name']) : uniqid(). $extension;
            $filename = str_replace("'", '', $filename);
            $filepath = $uploadDir . '/' . $filename;
    
            //save the file to a particular folder
            if (count($error) || !move_uploaded_file($file['tmp_name'], $filepath)) {
                $e['file_save'] = 'Failed to save file';
            }
            
            // Process image with specified dimensions if it's an image
            $processedImagePath = $filepath;
            $processedImageSize = $file['size'];
    
            //prepare media data for media table to be inserted
            if(empty($e)){
                $filesData[] = [
                    'file' => json_encode([
                        'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                        'name' => basename($processedImagePath),
                        'type' => $file['type'],
                        'size' => $processedImageSize,
                        'mime_type' => $file['type'],
                        'objectURL' => DS . $uploadDirName . DS . basename($processedImagePath)
                    ]),
                    'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'type' => $file['type'],
                    'folder_id' => $folder_id,
                    'meta' => json_encode([
                        'original_name' => $file['name'],
                        'size' => $processedImageSize,
                        'mime_type' => $file['type'],
                        'original_size' => $file['size']
                    ])
                ];
                $filesResponseData[] = [
                    'path' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'name' => basename($processedImagePath),
                    'image' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'description' => $file['description'] ?? "",
                    'size' => $processedImageSize,
                    'type' => $file['type'],
                    'objectURL' => DS . $uploadDirName . DS . basename($processedImagePath),
                    'file' => $file,
                    'status' => ['name' => 'Uploaded', 'severity' => 'success'],
                    'media_id' => null,
                    'format' => $file['format'] ?? ''
                ];
            }else{
                $error[$file['name']] = $e;
            }
        }

        //insert the media data into the media table
        try{
            if(count($filesData)){
                // $this->model->insert($filesData);
               $this->model->upsert($filesData, ['path']);
               $path = $filesData[0]['path'];
               $mediaId = $this->model->where('path', '=', $path)->select(['media_id'])->first();
               $filesResponseData[0]['media_id'] = $mediaId ? $mediaId->media_id : null;
               return ['files' => $filesResponseData, 'error' => $error];
            }
        }catch(\PDOException $e){
            return null;
        }

        return ['files' => $filesResponseData, 'error' => $error];
    }

    /**
     * Fail-safe same-size WebP: decode jpg/png/gif with GD, write sibling .webp, remove original.
     * Use when `processImageWithSize()` is not run (no target canvas). On any skip or error the file is unchanged.
     *
     * @return array{filepath: string, filename: string, type: string, size: int}|null
     */
    private function tryConvertRasterToWebpSameDimensions(string $filepath): ?array
    {
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $convertableExts = ['png', 'jpg', 'jpeg', 'gif'];
        if ($ext === 'webp' || !in_array($ext, $convertableExts, true)) {
            return null;
        }
        if (!function_exists('imagewebp')) {
            return null;
        }

        try {
            $loaded = $this->loadImageResourceFromPath($filepath);
            if ($loaded === null) {
                return null;
            }
            $src = $loaded['resource'];
            $imageType = $loaded['type'];

            $width = imagesx($src);
            $height = imagesy($src);
            $dest = imagecreatetruecolor($width, $height);
            if ($dest === false) {
                imagedestroy($src);

                return null;
            }
            // preserve transparency for PNG and GIF
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
                imagealphablending($dest, false);
                imagesavealpha($dest, true);
                $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
                imagefilledrectangle($dest, 0, 0, $width, $height, $transparent);
            }
            imagecopy($dest, $src, 0, 0, 0, 0, $width, $height);
            $webpPath = preg_replace('/\.[^.]+$/i', '.webp', $filepath);
            if (!imagewebp($dest, $webpPath, 90)) {
                imagedestroy($src);
                imagedestroy($dest);

                return null;
            }
            imagedestroy($src);
            imagedestroy($dest);
            @unlink($filepath);
            $newSize = filesize($webpPath);

            return [
                'filepath' => $webpPath,
                'filename' => basename($webpPath),
                'type' => 'image/webp',
                'size' => $newSize !== false ? $newSize : 0,
            ];
        } catch (\Throwable $convEx) {
            // keep original file on conversion failure
            return null;
        }
    }

    /**
     * Compute “contain” geometry: scale uniformly so the full source fits inside the box (no cropping).
     *
     * Uses the smaller of (targetW/srcW) and (targetH/srcH) so one dimension touches the box and the other has margin.
     *
     * @return array{dst_w: int, dst_h: int, offset_x: int, offset_y: int} Draw size and top-left offset on the canvas.
     */
    private function computeContainLayout(int $srcW, int $srcH, int $targetW, int $targetH): array
    {
        if ($srcW < 1 || $srcH < 1 || $targetW < 1 || $targetH < 1) {
            return ['dst_w' => 1, 'dst_h' => 1, 'offset_x' => 0, 'offset_y' => 0];
        }
        // Single scale for both axes — guarantees aspect ratio; may letterbox either horizontally or vertically.
        $scale = min($targetW / $srcW, $targetH / $srcH);
        $dstW = max(1, (int) round($srcW * $scale));
        $dstH = max(1, (int) round($srcH * $scale));
        // Center the fitted bitmap on the fixed-size canvas (bars will be filled with white in the composer).
        $offsetX = (int) (($targetW - $dstW) / 2);
        $offsetY = (int) (($targetH - $dstH) / 2);

        return ['dst_w' => $dstW, 'dst_h' => $dstH, 'offset_x' => $offsetX, 'offset_y' => $offsetY];
    }

    /**
     * Build one truecolor canvas: white background, then resampled source centered (contain, no distortion).
     *
     * Alpha from PNG/GIF is blended onto white so the final WebP matches the letterbox look.
     *
     * @param resource|\GdImage $sourceImage
     * @return resource|\GdImage|null
     */
    private function composeContainOnWhiteCanvas($sourceImage, int $srcW, int $srcH, int $canvasW, int $canvasH)
    {
        $layout = $this->computeContainLayout($srcW, $srcH, $canvasW, $canvasH);
        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        if ($canvas === false) {
            return null;
        }
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $canvasW, $canvasH, $white);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, false);
        // Down/upscale into the fitted rect; offsets leave equal margins when aspect ratios differ.
        imagecopyresampled(
            $canvas,
            $sourceImage,
            $layout['offset_x'],
            $layout['offset_y'],
            0,
            0,
            $layout['dst_w'],
            $layout['dst_h'],
            $srcW,
            $srcH
        );

        return $canvas;
    }

    /**
     * Scale to a fixed width; height is derived from the source aspect ratio (uniform scale — no crop, stretch, or padding).
     *
     * @param resource|\GdImage $sourceImage
     * @return resource|\GdImage|null
     */
    private function resizeImageToWidthPreserveAspectRatio($sourceImage, int $srcW, int $srcH, int $targetWidth)
    {
        if ($srcW < 1 || $srcH < 1 || $targetWidth < 1) {
            return null;
        }
        $targetHeight = max(1, (int) round($srcH * ($targetWidth / $srcW)));
        $dest = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($dest === false) {
            return null;
        }
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefill($dest, 0, 0, $transparent);
        imagealphablending($dest, true);
        imagecopyresampled($dest, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcW, $srcH);

        return $dest;
    }

    /**
     * Load a JPEG/PNG/GIF/WebP file into a GD image resource for resampling.
     *
     * @return array{resource: resource|\GdImage, type: int}|null
     */
    private function loadImageResourceFromPath(string $imagePath): ?array
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return null;
        }
        $imageType = $imageInfo[2];
        $sourceImage = null;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_WEBP:
                if (!function_exists('imagecreatefromwebp')) {
                    return null;
                }
                $sourceImage = imagecreatefromwebp($imagePath);
                break;
            default:
                return null;
        }
        if (!$sourceImage) {
            return null;
        }

        return ['resource' => $sourceImage, 'type' => $imageType];
    }

    /**
     * After writing `.webp`, drop the previous file if the extension changed (e.g. `.jpg` → `.webp`).
     */
    private function replaceWithWebpOutput(string $originalPath, string $webpPath): void
    {
        if ($originalPath !== $webpPath && is_file($originalPath)) {
            @unlink($originalPath);
        }
    }

    /**
     * Fit image into `$targetWidth`×`targetHeight` without cropping: scale to contain, pad with white, output WebP.
     * When `$is_banner` is true, scales to `BANNER_TARGET_WIDTH` (1920px) with height from aspect ratio (no padding).
     *
     * Optional `$size['featured_image_one|two']` repeats the same logic on extra canvases for alternate sizes.
     *
     * @param string $imagePath Path to the original image (may be replaced by a .webp sibling)
     * @return array|null Processed file metadata or null if loading/saving fails
     */
    private function processImageWithSize(string $imagePath, int $targetWidth, int $targetHeight, ?array $size = [], $is_banner = false): ?array
    {
        try {
            $loaded = $this->loadImageResourceFromPath($imagePath);
            if ($loaded === null) {
                return null;
            }
            $sourceImage = $loaded['resource'];
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Final main asset always uses .webp next to the upload (basename changes for DB/URLs).
            $webpPath = preg_replace('/\.[^.]+$/i', '.webp', $imagePath);

            // Primary output: fixed canvas, image centered, aspect ratio preserved.

            if ($is_banner) {
                $newImage = $this->resizeImageToWidthPreserveAspectRatio(
                    $sourceImage,
                    $originalWidth,
                    $originalHeight,
                    self::BANNER_TARGET_WIDTH
                );
                if ($newImage === null) {
                    imagedestroy($sourceImage);

                    return null;
                }
            } else {
                $newImage = $this->composeContainOnWhiteCanvas($sourceImage, $originalWidth, $originalHeight, $targetWidth, $targetHeight);
                if ($newImage === null) {
                    imagedestroy($sourceImage);
                    return null;
                }
            }

            // Same numbers as inside composeContainOnWhiteCanvas — used only for stored metadata (fitted vs canvas).
            if ($is_banner) {
                $layout = [
                    'dst_w' => imagesx($newImage),
                    'dst_h' => imagesy($newImage),
                    'offset_x' => 0,
                    'offset_y' => 0,
                ];
            } else {
                $layout = $this->computeContainLayout($originalWidth, $originalHeight, $targetWidth, $targetHeight);
            }
            $featuredOnePath = null;
            $featuredTwoPath = null;

            // // Optional extra renditions: same contain + white + WebP, different dimensions (e.g. blog cards).
            // if (isset($size['featured_image_one']['width'], $size['featured_image_one']['height'])) {
            //     $fw = (int) $size['featured_image_one']['width'];
            //     $fh = (int) $size['featured_image_one']['height'];
            //     $featuredOnePath = preg_replace('/\.[^.]+$/i', '_featured_one.webp', $webpPath);
            //     $featOne = $this->composeContainOnWhiteCanvas($sourceImage, $originalWidth, $originalHeight, $fw, $fh);
            //     if ($featOne !== null) {
            //         imagewebp($featOne, $featuredOnePath, 90);
            //         imagedestroy($featOne);
            //     }
            // }
            // if (isset($size['featured_image_two']['width'], $size['featured_image_two']['height'])) {
            //     $fw = (int) $size['featured_image_two']['width'];
            //     $fh = (int) $size['featured_image_two']['height'];
            //     $featuredTwoPath = preg_replace('/\.[^.]+$/i', '_featured_two.webp', $webpPath);
            //     $featTwo = $this->composeContainOnWhiteCanvas($sourceImage, $originalWidth, $originalHeight, $fw, $fh);
            //     if ($featTwo !== null) {
            //         imagewebp($featTwo, $featuredTwoPath, 90);
            //         imagedestroy($featTwo);
            //     }
            // }

            // Featured variants consumed the same $sourceImage; free before encoding the main bitmap.
            imagedestroy($sourceImage);

            if (!function_exists('imagewebp') || !imagewebp($newImage, $webpPath, 90)) {
                imagedestroy($newImage);

                return null;
            }
            imagedestroy($newImage);

            // Remove jpg/png/gif original when we now only keep the .webp on disk.
            $this->replaceWithWebpOutput($imagePath, $webpPath);

            $processedSize = filesize($webpPath);

            return [
                'path' => $webpPath,
                'size' => $processedSize !== false ? $processedSize : 0,
                'dimensions' => [
                    'width' => $is_banner ? $layout['dst_w'] : $targetWidth,
                    'height' => $is_banner ? $layout['dst_h'] : $targetHeight,
                    'original_width' => $originalWidth,
                    'original_height' => $originalHeight,
                    'fitted_width' => $layout['dst_w'],
                    'fitted_height' => $layout['dst_h'],
                ],
                'featured_image_one' => $featuredOnePath,
                'featured_image_two' => $featuredTwoPath,
            ];
        } catch (Exception $e) {
            error_log('Image processing error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Build a fixed-size thumbnail: contain-fit + white letterbox (same behaviour as `processImageWithSize`), WebP when available.
     */
    public function createThumbnail(string $sourceImagePath, string $thumbnailDir, int $targetWidth, int $targetHeight): ?array
    {
        try {
            $imageInfo = getimagesize($sourceImagePath);
            if (!$imageInfo) {
                return null;
            }
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            $pathInfo = pathinfo($sourceImagePath);
            $originalName = str_replace("'", '', $pathInfo['filename']);
            $originalName = str_replace(' ', '_', $originalName);
            $thumbExt = function_exists('imagewebp') ? 'webp' : 'jpg';
            $thumbnailFilename = $originalName . '_thumblist.' . $thumbExt;

            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            $fullThumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;

            $canvas = $this->buildThumbnailCanvas($sourceImagePath, $targetWidth, $targetHeight);
            if ($canvas === null) {
                return null;
            }
            if (!$this->saveThumbnailBitmap($canvas, $fullThumbnailPath)) {
                imagedestroy($canvas);

                return null;
            }
            imagedestroy($canvas);

            $thumbnailSize = filesize($fullThumbnailPath);
            // If output is tiny, bump canvas 1.5× and regenerate once (same heuristic as before).
            if ($thumbnailSize !== false && $thumbnailSize < 61440) {
                if (file_exists($fullThumbnailPath)) {
                    unlink($fullThumbnailPath);
                }
                $targetWidth = (int) round($targetWidth * 1.5);
                $targetHeight = (int) round($targetHeight * 1.5);
                $canvas = $this->buildThumbnailCanvas($sourceImagePath, $targetWidth, $targetHeight);
                if ($canvas === null) {
                    return null;
                }
                if (!$this->saveThumbnailBitmap($canvas, $fullThumbnailPath)) {
                    imagedestroy($canvas);

                    return null;
                }
                imagedestroy($canvas);
                $thumbnailSize = filesize($fullThumbnailPath);
            }

            $layout = $this->computeContainLayout($originalWidth, $originalHeight, $targetWidth, $targetHeight);
            $mimeType = mime_content_type($fullThumbnailPath);
            if ($mimeType === false) {
                $mimeType = $thumbExt === 'webp' ? 'image/webp' : 'image/jpeg';
            }

            $relativePath = str_replace(ROOT_DIR . DS . 'public', '', $fullThumbnailPath);
            $relativePath = str_replace(DS, '/', $relativePath);

            return [
                'name' => $thumbnailFilename,
                'image' => $relativePath,
                'description' => '',
                'size' => $thumbnailSize !== false ? $thumbnailSize : 0,
                'type' => $mimeType,
                'objectURL' => $relativePath,
                'file' => [
                    'source_path' => $sourceImagePath,
                    'thumbnail_path' => $fullThumbnailPath,
                ],
                'dimensions' => [
                    'width' => $targetWidth,
                    'height' => $targetHeight,
                    'original_width' => $originalWidth,
                    'original_height' => $originalHeight,
                    'fitted_width' => $layout['dst_w'],
                    'fitted_height' => $layout['dst_h'],
                ],
                'target_size' => ['width' => $targetWidth, 'height' => $targetHeight],
                'status' => ['name' => 'Uploaded', 'severity' => 'success'],
            ];
        } catch (Exception $e) {
            error_log('Thumbnail creation error: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Load source via `loadImageResourceFromPath`, then contain + white canvas at $targetW × $targetH.
     *
     * @return resource|\GdImage|null
     */
    private function buildThumbnailCanvas(string $sourceImagePath, int $targetWidth, int $targetHeight)
    {
        $loaded = $this->loadImageResourceFromPath($sourceImagePath);
        if ($loaded === null) {
            return null;
        }
        $src = $loaded['resource'];
        $w = imagesx($src);
        $h = imagesy($src);
        $canvas = $this->composeContainOnWhiteCanvas($src, $w, $h, $targetWidth, $targetHeight);
        imagedestroy($src);

        return $canvas;
    }

    /**
     * Prefer WebP; if GD has no WebP encoder, fall back to JPEG (path should use .jpg).
     */
    private function saveThumbnailBitmap($canvas, string $fullPath): bool
    {
        if (function_exists('imagewebp')) {
            return imagewebp($canvas, $fullPath, 90);
        }

        return imagejpeg($canvas, $fullPath, 90);
    }

    /**
     * Delete media items by filename
     * 
     * @param string $filename The filename to match for deletion
     * @return array Information about the deletion operation
     */
    public function deleteByFilename(string $filename): array
    {
        // Find all media with the matching filename
        $mediaItems = $this->model->where('file', 'LIKE', '%' . $filename . '%')->findAll();
        
        if (empty($mediaItems)) {
            return [
                'success' => false,
                'message' => 'No media files found with the given filename',
                'deleted_count' => 0
            ];
        }
        
        $deletedIds = [];
        $deletedFiles = 0;
        
        foreach ($mediaItems as $media) {
            // Delete the physical file if it exists
            $filePath = ROOT_DIR . DS . 'public' . $media['file'];
            if (file_exists($filePath) && is_file($filePath)) {
                if (unlink($filePath)) {
                    $deletedFiles++;
                }
            }
            
            // Delete the database record
            if ($this->model->delete($media['media_id'])) {
                $deletedIds[] = $media['media_id'];
            }
        }
        
        return [
            'success' => count($deletedIds) > 0,
            'message' => count($deletedIds) > 0 ? 'Media deleted successfully' : 'Failed to delete media',
            'deleted_count' => count($deletedIds),
            'deleted_ids' => $deletedIds,
            'deleted_files' => $deletedFiles
        ];
    }

    public function deleteMediaByPath(string $path): bool
    {
        $media = $this->model->where('`file`', '=', $path)->first();
        if(!$media){
            return false;
        }
        return $this->model->delete($media->media_id);
    }
    // public function deleteMediaById(int $id, string $path = ''): bool
    // {
    //     $media = $this->model->where('media_id', '=', $id)->first();
    //     if(!$media){
    //         return false;
    //     }
    //     //Delete the physical file from the path
    //     $file = json_decode($media->file, true);
    //     if(isset($file['path'])){
    //         $filePath = ROOT_DIR . DS . 'public' . DS . $file['path'];
    //         if(file_exists($filePath) && is_file($filePath)){
    //             unlink($filePath);
    //         }
    //     }

    //     return $this->model->delete($media->media_id);
    // }

    public function deleteMediaById(int $id, string $path = ''): bool
    {
        if (!empty($path)) {

            // $filePath = ROOT_DIR . DS . 'public' . $path;
            $filePath = $path;
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
            return true;
        }

        return false;
        // delete DB record
        // $media = $this->model->where('media_id', '=', $id)->first();

        // if (!$media) {
        //     return false;
        // }

        // return $this->model->delete($media->media_id);
    }

    public function getFolders(): array
    {
        $this->model->clearQuery();
        $query = $this->model
            ->where('type', '=', 'folder')
            ->whereNull('media.parent_id')
            ->orderBy('media_id', 'ASC');
        $results = $query->findAll();
        
        foreach ($results as &$folder) {
            if (isset($folder['meta']) && $folder['meta']) {
                $folder['meta'] = json_decode($folder['meta'], true);
            }
            $file = json_decode($folder['file'], true);
            if(isset($file['path'])){
                $folder['file_count'] = $this->countFilesByFolderPath($file['path']);
            }
        }

        $results['total_folders'] = count($results);
        return $results;
    }


    public function countFilesByFolderPath(string $path): int
    {
        // Construct the full filesystem path
        $fullPath = ROOT_DIR . DS . 'public' . DS . $path;
        
        // Check if the folder exists
        if (!is_dir($fullPath)) {
            return 0;
        }
        
        // Scan the folder and count files
        $count = 0;
        $files = scandir($fullPath);
        
        foreach ($files as $file) {
            // Skip . and .. directories
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $filePath = $fullPath . DS . $file;
            
            // Count only files, not directories
            if (is_file($filePath)) {
                $count++;
            }
        }
        
        return $count;
    }



    public function getSubFolders(int $parentId, string $sub_folder_name = ''): array
    {
        // Get current folder from DB
        $currentFolder = $this->model->where('media_id', '=', $parentId)->first();
    
        if (!$currentFolder) {
            return [];
        }

        $folderPath = '';
        if($sub_folder_name){
            $folderPath = $sub_folder_name;
        }else{
            $folderPath = ltrim($currentFolder->data->path, '/');
        }

        $data = $this->getSubFoldersData($folderPath);

        return [
            'currentFolder' => $folderPath,
            'breadcrumb' => $this->generateBreadcrumb($folderPath),
            'media_folders' => $this->getFolders(),
            'folders' => $data['folders'],
            'files' => $data['files']
        ];
    }

    private function generateBreadcrumb(string $folderPath): array
    {
        $parts = explode('/', trim($folderPath, '/'));
        $breadcrumbs = [];
        $currentPath = '';
        $total = count($parts);
    
        foreach ($parts as $index => $part) {
            $currentPath .= ($currentPath ? '/' : '') . $part;
    
            $breadcrumbs[] = [
                'name' => $part,
                'path' => ($index === $total - 1) ? '#' : $currentPath
            ];
        }
    
        return $breadcrumbs;
    }


    public function getSubFoldersData(string $folderPath): array
    {
        $fullFolderPath = ROOT_DIR . '/public/' . $folderPath;
    
        // Ensure folder exists
        if (!is_dir($fullFolderPath)) {
           throw new UnauthorizedHttpException('Folder not found');
        }
    
        $folders = [];
        $files = [];
        $items = scandir($fullFolderPath);
    
        $FILE_FORMAT_IMAGES = [
            'gsm' => '/media/design-resource/icons/gsm.png',
            'dwg' => '/media/design-resource/icons/dwg.png',
            'max' => '/media/design-resource/icons/max.png',
            'skp' => '/media/design-resource/icons/skp.png',
            'rfa' => '/media/design-resource/icons/rfa.png',
            'zip' => '/media/design-resource/icons/zip.png',
            'doc' => '/media/design-resource/icons/doc.png',
            'docx' => '/media/design-resource/icons/docx.png',
            'xls' => '/media/design-resource/icons/xls.png',
            'xlsx' => '/media/design-resource/icons/xlsx.png',
            'ppt' => '/media/design-resource/icons/ppt.png',
            'pptx' => '/media/design-resource/icons/pptx.png'
        ];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
    
            $fullPath = $fullFolderPath . '/' . $item;
    
            if (is_dir($fullPath)) {
                $folders[] = [
                    'name' => $item,
                    'path' => '/media/' . $item, // relative path for DB
                    'type' => 'folder'
                ];
            } elseif (is_file($fullPath)) {
                $relativePath = '/' . $folderPath . '/' . $item;

                // get extension of the file
                $icon = $relativePath;
                $dataType = 'file';
                $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                if($folderPath == 'media/design-resource/models' && $extension == 'zip'){
                    $dataType = 'models';
                       $foundFormats = $this->checkAllowedFormats($item);
                       if($foundFormats){
                            $foundFormats = strtolower(trim($foundFormats));
                            $icon = $FILE_FORMAT_IMAGES[$foundFormats];
                       }else{
                            $icon = $FILE_FORMAT_IMAGES[$extension];
                       }
                }elseif($folderPath == 'media/design-resource/models'){
                    $dataType = 'models';
                    $icon = isset($FILE_FORMAT_IMAGES[$extension]) ? $FILE_FORMAT_IMAGES[$extension] : $relativePath;
                }

                // extension not found
                if(!isset($extension)){
                    $icon = '/media/design-resource/icons/not-found.png';
                }

                $files[] = [
                    'id' => null,
                    'data_type' => $dataType,
                    'file' => [
                        'data_type' => $dataType,
                        'name' => $item,
                        'size' => filesize($fullPath),
                        'type' => mime_content_type($fullPath),
                        'extension' => $extension,
                        'error' => 0,
                        'tmp_name' => $fullPath,
                        'full_path' => $item
                    ],
                    'name' => $item,
                    'size' => filesize($fullPath),
                    'type' => mime_content_type($fullPath),
                    'image' => $icon,
                    'extension' => $extension,
                    'status' => [
                        'name' => 'Uploaded',
                        'severity' => 'success'
                    ],
                    'media_id' => null,
                    'objectURL' => $icon,
                    'created_at' => '',
                    'description' => '',
                    'product_image_id' => null
                ];
            }
        }

        return [
            'folders' => $folders,
            'files' => $files
        ];
    }

    private function checkAllowedFormats(string $item): string
    {
        $allowedFormats = ['SKP', 'DWG', 'Max', 'GSM'];
    
        // Get the base file name (without extension)
        $baseName = pathinfo($item, PATHINFO_FILENAME);
    
        // Extract all uppercase words (potential formats)
        preg_match_all('/\b([A-Z]{2,})\b/', $baseName, $matches);
    
        // Keep only allowed formats
        $foundFormats = array_intersect($matches[1], $allowedFormats);
    
        // Return the formats as a comma-separated string, or empty if none found
        return !empty($foundFormats) ? implode(', ', $foundFormats) : '';
    }

    public function getSubFolders_backup(int $parentId): array
    {
        $currentFolder = $this->model->where('media_id', '=', $parentId)->first();
        $this->model->clearQuery();
        $query = $this->model
            ->where('type', '=', 'folder')
            ->where('parent_id', '=', $parentId)
            ->orderBy('media_id', 'DESC');
        $result = $query->findAll();
        
        // Decode meta JSON for each folder
        foreach ($result as &$folder) {
            if (isset($folder['meta']) && $folder['meta']) {
                $folder['meta'] = json_decode($folder['meta'], true);
                $file = json_decode($folder['file'], true);
                if(isset($file['path'])){
                    $folder['file_count'] = $this->countFilesByFolderPath($file['path']);
                }
            }
        }
        $files = $this->getFilesByFolderId($parentId);
        
        return ['currentFolder' => $currentFolder, 'folders' => $result, 'files' => $files];
    }

    public function getFilesByFolderId(int $folderId): array
    {
        $this->model->clearQuery();
        $query = $this->model
            ->where('type', 'LIKE', 'image%')
            ->where('folder_id', '=', $folderId)
            ->orderBy('media_id', 'DESC');
        $result = $query->findAll();
        foreach ($result as &$file) {
            if (isset($file['meta']) && $file['meta']) {
                $file['meta'] = json_decode($file['meta'], true);
            }
        }
        return $result;
    }

    public function getAllFiles(): array
    {
        $query = $this->model
            ->where('type', 'LIKE', 'image%')
            ->orderBy('media_id', 'DESC');
        $result = $query->findAll();
        foreach ($result as &$file) {
            if (isset($file['meta']) && $file['meta']) {
                $file['meta'] = json_decode($file['meta'], true);
            }
        }
        return $result;
    }

    public function createFolder(array $folder): ?array
    {       
        $newFolderPath = trim($folder['path'], '/');
        $folderData = [
            'file' => $folder['file'],
            'type' => 'folder',
            'path' => $newFolderPath,
            'meta' => json_encode($folder['meta'] ?? []),
            'parent_id' => $folder['media_id'] ?? null,
            'name' => $folder['name'] ?? '',
            'folder_id' => $folder['folder_id'] ?? 0,
        ];

        $newFolderPath = ROOT_DIR . DS . 'public' . DS . $newFolderPath;
        if (!is_dir($newFolderPath)) {
            mkdir($newFolderPath, 0755, true);
        }
        // Create the folder using the model's create method       
        if (!$folder['folder_id']) {    
            $createdFolder = $this->model->create($folderData);
            $folderData['media_id'] = $createdFolder->media_id;
            $folderData['file_count'] = 0;
            return $folderData;
            // return $this->getFolders();
        }else{
            return $folderData;
        }

    }

    public function createFolder_old(array $folder): ?array
    {
        $newFolderPath = trim($folder['path'], '/');
        if (!$folder['folder_id']) {    
            $createdFolder = $this->model->create($folder);
            $folder['media_id'] = $createdFolder->media_id;
            $folder['file_count'] = 0;
        }else{
            $newFolderPath = ROOT_DIR . DS . 'public' . DS . $newFolderPath;
            if (!is_dir($newFolderPath)) {
                mkdir($newFolderPath, 0755, true);
            }
        }

        // Return the created folder data
        return $folder;
    }

    public function getCategories(): array
    {
        // Get total images
        $imageQuery = $this->model->where('type', 'LIKE', 'image%');
        $totalImages = $imageQuery->countAll();

        // Get total documents
        $documentQuery = $this->model->where('type', '=', 'document');
        $totalDocuments = $documentQuery->countAll();

        // Get total videos
        $videoQuery = $this->model->where('type', '=', 'video');
        $totalVideos = $videoQuery->countAll();

        return [
            'images' => $totalImages,
            'documents' => $totalDocuments,
            'videos' => $totalVideos
        ];
    }

    public function deleteFolder(int $id): bool
    {
        // 1. Find the folder in the database
        $model = $this->model->clearQuery();
        $folder = $this->model->where('media_id', '=', $id)->first();
        if (!$folder) {
            return false;
        }

        // 2. Get the folder path from the file field (JSON)
        $file = json_decode($folder->file, true);
        $folderPath = isset($file['path']) ? $file['path'] : null;

        // 3. Find all media items (files and folders) whose folder_id is this folder's media_id
        $this->model->clearQuery();
        $children = $this->model->where('folder_id', '=', $id)->orWhere('parent_id', '=', $id)->findAll();

        foreach ($children as $child) {
            if ($child['type'] === 'folder') {
                // 5. For subfolders: recursively delete
                $childDirPath = json_decode($child['file'], true)['path'];
                $dirPath = ROOT_DIR . DS . 'public' . DS . ltrim($childDirPath, DS);
                if (is_dir($dirPath)) {
                    //Delete all Child records including folders and files
                    $this->deleteDirectoryRecursively($dirPath, $childDirPath);
                }
            } else {
                // 4. For files: delete both database row and physical file
                $fileData = json_decode($child['file'], true);
                if (isset($fileData['path'])) {
                    $filePath = ROOT_DIR . DS . 'public' . DS . ltrim($fileData['path'], DS);
                    if (file_exists($filePath) && is_file($filePath)) {
                        unlink($filePath);
                    }
                }
                $this->model->delete($child['media_id']);
            }
        }

        // 6. Delete Parent Directory
        if ($folderPath) {
            $dirPath = ROOT_DIR . DS . 'public' . DS . ltrim($folderPath, DS);
            if (is_dir($dirPath)) {
                //Delete all Child records including folders and files
                $this->deleteDirectoryRecursively($dirPath, $folderPath);
            }
        }



        // 7. Delete the folder's row from the database
        $this->model->clearQuery();
        return $this->model->delete($folder->media_id);
    }


    public function getInstagramSliderComponentData(array $param)
    {
        $model = 'media';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model
                ->join('media_content', 'media_content.media_id', '=', 'media.media_id')
                ->where('media.type', 'LIKE', 'image%')
                ->where('media_content.language_id', '=', $param['language_id'] ?? 1)
                ->select([
                    'media.media_id as id',
                    'media_content.name',
                    'media.file as image',
                    'media.meta'
                ]);

            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }

            $query->orderBy('media.media_id', 'DESC');
            $results = $query->findAll();

            $finalResults = [];
            foreach ($results as $result) {
                // Process image data from JSON file field
                $imageData = json_decode($result['image'] ?? '{}', true);
                $imageUrl = $imageData['objectURL'] ?? '/img/product-detail/insta-default.png';
                
                // Process meta data for Instagram link
                $metaData = json_decode($result['meta'] ?? '{}', true);
                $instaLink = $metaData['instagram_link'] ?? 'https://www.instagram.com/archi_furniture/';
                
                $finalResults[] = [
                    'id' => $result['id'],
                    'name' => $result['name'] ?? 'Instagram Post',
                    'image' => $imageUrl,
                    'instaLink' => $instaLink
                ];
            }

            return $finalResults;
        }
        return [];
    }

    public function getOurHistoryMasonryComponentData()
    {
        $query = $this->model
            ->join('media_content', 'media_content.media_id', '=', 'media.media_id')
            ->where('media.type', 'LIKE', 'image%')
            ->where('media_content.language_id', '=', 1) // Default language
            ->select([
                'media.media_id',
                'media_content.name as heading',
                'media.file as image',
                'media_content.description as des',
                'media.meta'
            ]);

        $query->orderBy('media.media_id', 'ASC')
              ->limit(4);

        $results = $query->findAll();

        $finalResults = [];
        foreach ($results as $index => $result) {
            // Process image data from JSON file field
            $imageData = json_decode($result['image'] ?? '{}', true);
            $imageUrl = $imageData['objectURL'] ?? '/img/about/gallery-image' . ($index + 1) . '.png';
            
            // Determine grid class based on index (alternating between 7 and 6 columns)
            $gridClass = ($index % 2 == 0) ? 'grid-col-span-7' : 'grid-col-span-6';
            
            // Generate style with transform and padding for masonry effect
            $transformY = $index * 45 + 5; // Varying transform values
            $paddingTop = 0; // Consistent padding
            $style = "transform: translateY({$transformY} px);padding-top:{$paddingTop} px";

            // Generate link text based on heading
            $linkText = 'View all ' . strtolower($result['heading'] ?? 'History Item');
            
            $finalResults[] = [
                'heading' => $result['heading'] ?? 'History Item ' . ($index + 1),
                'img' => $imageUrl,
                'des' => $result['des'] ?? 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
                'link_text' => $linkText,
                'class' => $gridClass,
                'style' => $style
            ];
        }

        // If no results found, return default data
        if (empty($finalResults)) {
            $finalResults = [
                [
                    'heading' => 'World-class product display',
                    'img' => '/img/about/gallery-image1.png',
                    'des' => 'A full collection of workstations from leg-based systems to panel constructions and height-adjustable offerings. Find the perfect configuration and aesthetic for your space.',
                    'link_text' => 'View all World-class product display',
                    'class' => 'grid-col-span-7',
                    'style' => 'transform: translateY(0 px);padding-top:0 px'
                ],
                [
                    'heading' => 'unparalleled service',
                    'img' => '/img/about/gallery-image2.png',
                    'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis',
                    'link_text' => 'View all unparalleled service',
                    'class' => 'grid-col-span-6',
                    'style' => 'transform: translateY(49 px);padding-top:0 px'
                ],
                [
                    'heading' => 'Product certifications',
                    'img' => '/img/about/gallery-image3.png',
                    'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
                    'link_text' => 'View all Product certifications',
                    'class' => 'grid-col-span-6',
                    'style' => 'transform: translateY(95 px);padding-top:0 px'
                ],
                [
                    'heading' => 'Product warranty',
                    'img' => '/img/about/gallery-image4.png',
                    'des' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
                    'link_text' => 'View all Product warranty',
                    'class' => 'grid-col-span-7',
                    'style' => 'transform: translateY(130 px);padding-top:0 px'
                ]
            ];
        }

        return ['items' => $finalResults];
    }

    public function getVideoGalleryWhoWeAreComponentData(array $param = [])
    {
        $query = $this->model
            ->join('media_content', 'media_content.media_id', '=', 'media.media_id')
            ->where('media.type', 'LIKE', 'video%')
            ->where('media_content.language_id', '=', $param['language_id'] ?? 1)
            ->select([
                'media.media_id',
                'media.file',
                'media.meta',
                'media_content.name',
                'media_content.caption',
                'media_content.description'
            ]);

        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }

        $query->orderBy('media.media_id', 'ASC');
        $results = $query->findAll();

        $finalResults = [];
        foreach ($results as $index => $result) {
            $fileData = json_decode($result['file'] ?? '{}', true);
            $metaData = json_decode($result['meta'] ?? '{}', true);

            // Determine video source and type
            $src = $fileData['objectURL'] ?? $fileData['url'] ?? '';
            $poster = $metaData['poster'] ?? '/img/vimeo-video-poster.jpg';
            $thumb = $metaData['thumb'] ?? $poster;
            $size = $metaData['size'] ?? null;

            // Generate subHtml content
            $title = $result['name'] ?? 'Video ' . ($index + 1);
            $description = $result['description'] ?? 'Video description for ' . $title;
            $subHtml = '<h4>' . $title . '</h4><p>' . $description . '</p>';

            $item = [
                'src' => $src,
                'poster' => $poster,
                'thumb' => $thumb,
                'subHtml' => $subHtml
            ];

            // Add size if available
            if ($size) {
                $item['size'] = $size;
            }

            $finalResults[] = $item;
        }

        // If no results found, return default structure
        if (empty($finalResults)) {
            $finalResults = [
                [
                    'src' => '//vimeo.com/112836958',
                    'poster' => '/img/vimeo-video-poster.jpg',
                    'thumb' => '/img/vimeo-video-poster.jpg',
                    'subHtml' => '<h4>Nature</h4><p>Video by <a target="_blank" href="https://vimeo.com/charliekaye">Charlie Kaye</a></p>'
                ],
                [
                    'size' => '1280-720',
                    'src' => '//www.youtube.com/watch?v=EIUJfXk3_3w',
                    'poster' => 'https://img.youtube.com/vi/EIUJfXk3_3w/maxresdefault.jpg',
                    'thumb' => 'https://img.youtube.com/vi/EIUJfXk3_3w/maxresdefault.jpg',
                    'subHtml' => '<h4>Puffin Hunts Fish To Feed Puffling | Blue Planet II | BBC Earth</h4><p>This puffin parent must go out to sea to feed his chick, but he must evade other birds that would rob him.</p>'
                ],
                [
                    'src' => 'img/image-1.avif',
                    'thumb' => 'img/thumb1.avif',
                    'subHtml' => '<div class="lightGallery-captions"><h4>Caption 1</h4><p>Description of the slide 1</p></div>'
                ],
                [
                    'src' => 'img/image-2.avif',
                    'thumb' => 'img/thumb2.avif',
                    'subHtml' => '<div class="lightGallery-captions"><h4>Caption 2</h4><p>Description of the slide 2</p></div>'
                ]
            ];
        }

        return $finalResults;
    }

    public function getVideoGalleryManufacturingProcessComponentData(array $param = [])
    {
        $query = $this->model
            ->join('media_content', 'media_content.media_id', '=', 'media.media_id')
            ->where('media.type', 'LIKE', 'video%')
            ->where('media_content.language_id', '=', $param['language_id'] ?? 1)
            ->select([
                'media.media_id',
                'media.file',
                'media.meta',
                'media_content.name',
                'media_content.caption',
                'media_content.description'
            ]);

        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }

        $query->orderBy('media.media_id', 'ASC');
        $results = $query->findAll();

        $finalResults = [];
        foreach ($results as $index => $result) {
            $fileData = json_decode($result['file'] ?? '{}', true);
            $metaData = json_decode($result['meta'] ?? '{}', true);

            // Determine video source and type
            $src = $fileData['objectURL'] ?? $fileData['url'] ?? '';
            $poster = $metaData['poster'] ?? '/img/vimeo-video-poster.jpg';
            $thumb = $metaData['thumb'] ?? $poster;
            $size = $metaData['size'] ?? null;

            // Generate subHtml content
            $title = $result['name'] ?? 'Video ' . ($index + 1);
            $description = $result['description'] ?? 'Video description for ' . $title;
            $subHtml = '<h4>' . $title . '</h4><p>' . $description . '</p>';

            $item = [
                'src' => $src,
                'poster' => $poster,
                'thumb' => $thumb,
                'subHtml' => $subHtml
            ];

            // Add size if available
            if ($size) {
                $item['size'] = $size;
            }

            $finalResults[] = $item;
        }

        // If no results found, return default structure
        if (empty($finalResults)) {
            $finalResults = [
                [
                    'src' => '//vimeo.com/112836958',
                    'poster' => '/img/vimeo-video-poster.jpg',
                    'thumb' => '/img/vimeo-video-poster.jpg',
                    'subHtml' => '<h4>Nature</h4><p>Video by <a target="_blank" href="https://vimeo.com/charliekaye">Charlie Kaye</a></p>'
                ],
                [
                    'size' => '1280-720',
                    'src' => '//www.youtube.com/watch?v=EIUJfXk3_3w',
                    'poster' => 'https://img.youtube.com/vi/EIUJfXk3_3w/maxresdefault.jpg',
                    'thumb' => 'https://img.youtube.com/vi/EIUJfXk3_3w/maxresdefault.jpg',
                    'subHtml' => '<h4>Puffin Hunts Fish To Feed Puffling | Blue Planet II | BBC Earth</h4><p>This puffin parent must go out to sea to feed his chick, but he must evade other birds that would rob him.</p>'
                ],
                [
                    'src' => 'img/image-1.avif',
                    'thumb' => 'img/thumb1.avif',
                    'subHtml' => '<div class="lightGallery-captions"><h4>Caption 1</h4><p>Description of the slide 1</p></div>'
                ],
                [
                    'src' => 'img/image-2.avif',
                    'thumb' => 'img/thumb2.avif',
                    'subHtml' => '<div class="lightGallery-captions"><h4>Caption 2</h4><p>Description of the slide 2</p></div>'
                ]
            ];
        }

        return $finalResults;
    }

    /**
     * Recursively delete a directory and all its contents
     */
    private function deleteDirectoryRecursively($dir, $folderPath): void
    {
        if (!file_exists($dir)) {
            return;
        }
        if (!is_dir($dir)) {
            unlink($dir);
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DS . $item;
            if (is_dir($path)) {
                $folderPath = $folderPath . DS . $item;
                $this->deleteDirectoryRecursively($path, $folderPath);
            } else {
                //Retrive the file record using path from the database and delete
                $escapedFolderPath = addslashes($folderPath);
                $escapedItem = addslashes($item);
                $queryPath = '"' .'/'. $escapedFolderPath . '/' . $escapedItem . '"';
                $this->model->clearQuery();
                $mediaRecord = $this->model->whereJson('file', 'path', $queryPath)->first();
                if ($mediaRecord) {
                    $this->model->delete($mediaRecord->media_id);
                }
                unlink($path);
            }
        }
        //Delete dirctory record
        $escapedFolderPath = addslashes($folderPath);
        $this->model->clearQuery();
        $directoryRecord = $this->model->whereJson('file', 'path', $escapedFolderPath)->first();
        if ($directoryRecord) {
            $this->model->clearQuery();
            $this->model->delete($directoryRecord->media_id);
        }
        rmdir($dir);
    }

    public function getMediaById($id)
    {
        $query = $this->model
            ->join('media_content', 'media_content.media_id', '=', 'media.media_id')
            ->where('media.media_id', '=', $id ?? 1)
            // ->where('media.type', 'LIKE', 'video%')
            // ->where('media_content.language_id', '=', $param['language_id'] ?? 1)
            ->select([
                'media.media_id',
                'media.file',
                'media.meta',
                'media_content.name',
                'media_content.caption',
                'media_content.description'
            ])->first();

        return true;
    }
    
  
}