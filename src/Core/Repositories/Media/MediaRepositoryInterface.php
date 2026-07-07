<?php

declare(strict_types=1);

namespace App\Core\Repositories\Media;

use App\Core\Models\Media\Media;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface MediaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all media content with pagination and filtering
     * 
     * @param int|null $media_id Filter by media ID
     * @param int|null $language_id Filter by language ID
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @return array{data: array, total: int}
     */
    public function getAll(
        ?int $media_id = null,
        ?string $file = null,
        ?string $type = null,
        ?string $meta = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get media content by various criteria
     * 
     * @param int|null $media_id Filter by media ID
     * @param int|null $language_id Filter by language ID
     * @param string|null $file Filter by file name
     * @return Media|null
     */
    public function get(int $mediaId): ?Media;

    public function getImageMasonryGallery(array $param);

    public function uploadFile(array $data, ?int $folder_id = null, ?string $folder_path = null): ?array;


    public function upload(array $data, ?array $size = [], ?string $folder_path = null, ?int $folder_id = null, $is_banner = false, int $file_max_size = 25): ?array;

    public function uploadFiles(array $data, array $allowedTypes, ?string $folder_path = null, ?int $folder_id = null): ?array;

    

    /**4
     * Delete media items by filename
     * 
     * @param string $filename The filename to match for deletion
     * @return array Information about the deletion operation
     */
    public function deleteByFilename(string $filename): array;

    public function deleteMediaByPath(string $path): bool;

    public function deleteMediaById(int $id): bool;

    /**
     * Delete rows whose `path` column matches files under `$path`, and remove files from disk.
     *
     * @param string $path Folder relative to `public/` (e.g. media/uploads/2025/05)
     * @param list<array{name: string, media_id?: int|null}> $files File entries with basename and optional media id
     */
    public function deleteMediaByFileNames(string $path, array $files): bool;

    public function getFolders(): array;

    public function getSubFolders(int $parentId, string $sub_folder_name = ''): array;

    public function createFolder(array $folder): ?array;

    public function getCategories(): array;

    public function getFilesByFolderId(int $folderId): array;

    public function getAllFiles(): array;

    public function deleteFolder(int $id): bool;

    /**
     * Get Instagram slider data for Instagram slider component
     */
    public function getInstagramSliderComponentData(array $param);

    /**
     * Get our history masonry data for our history masonry component
     */
    public function getOurHistoryMasonryComponentData();

    /**
     * Get video gallery data for who we are video gallery component
     */
    public function getVideoGalleryWhoWeAreComponentData(array $param = []);

    /**
     * Get video gallery data for manufacturing process video gallery component
     */
    public function getVideoGalleryManufacturingProcessComponentData(array $param = []);

    public function createThumbnail(string $sourceImagePath, string $thumbnailPath, int $targetWidth, int $targetHeight): ?array;

    public function getMediaById(int $id);
} 