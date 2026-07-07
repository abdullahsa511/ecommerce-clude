<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class MediaController extends ApiController
{
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all media items with pagination and filtering.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $result = $this->mediaRepository->findAll();
        return $this->renderResponse($result);
    }

    /**
     * Get a media item by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $media = $this->mediaRepository->find((int)$id);
        if(!$media){
            return $this->renderError(404, 'Media not found');
        }
        return $this->renderResponse($media->data);
    }

    /**
     * Create a new media item.
     *
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        
        if($request->files() || isset($_FILES['files'])){
          $files = $request->files() ?? $_FILES['files'];
          
          if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
          }

          if($request->input('id') !== null){
            $uploadDir = 'media/sites/' . $request->input('id');
          }else{
            $uploadDir = $request->input('upload_dir', 'media/uploads' . date('Y/m'));
          }
          $data = [
            'files' => $files,
            // 'upload_dir' => $request->input('upload_dir', 'media/uploads' . date('Y/m')) 
            'upload_dir' => $uploadDir
          ];
          $size = json_decode($request->input('size', "[]"), true);

          $result = $this->mediaRepository->upload($data, $size, 'media/uploads');
          //   $result = $this->mediaRepository->upload($data, $size, 'media/sites');
          if(!$result){
            return $this->renderError(500, 'Failed to upload media');
          }
          return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }
    /**
     * Create a new media item.
     *
     * @param Request $request
     * @return Response
     */
    public function uploadToFolder(Request $request, $folder_id): Response
    {
        $breadcrumb = $request->input('breadcrumb');
        if($request->files() || isset($_FILES['files'])){
          $files = $request->files() ?? $_FILES['files'];

        //   $fileName = isset($files[0]['name']) ? $files[0]['name'] : '';
          $uploadPath = $breadcrumb;
          if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
          }
          
          $data = [
            'files' => $files,
            'upload_dir' => $uploadPath,
          ];
          //Retrieve the folder record of media table using the folder_id
          $folder = $this->mediaRepository->find((int)$folder_id);
          
          // 'upload_dir' => From the folder record get the folder path from the file column
          $path = json_decode($folder->file, true);
        //   if(!isset($path['path'])){
        //     return $this->renderError(422, 'Folder path not found');
        //   }


          $result = $this->mediaRepository->upload($data, null, $uploadPath, (int)$folder_id, null, 45);
          if(!$result){
            return $this->renderError(500, 'Failed to upload media');
          }
          return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }
    

    /**
     * Update a media item.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'file' => 'string|nullable',
                'type' => 'string|nullable',
                'meta' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingMedia = $this->mediaRepository->find((int)$id);
        if (!$existingMedia) {
            return $this->renderError(404, 'Media not found');
        }

        $media = $this->mediaRepository->update((int) $id, $data);
        if (!$media) {
            return $this->renderError(500, 'Failed to update media');
        }
        
        return $this->renderResponse($media->data);
    }

    /**
     * Delete a media item.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteFiles(Request $request): Response
    {
        $files = $request->input('files')??[];
        $path = $request->input('path');
        if(!$path){
            return $this->renderError(422, 'Path is required');
        }
        if(!count($files)){
            return $this->renderError(422, 'Media files are required');
        }
        $this->mediaRepository->deleteMediaByFileNames($path, (array)$files);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }

    public function delete(Request $request, $id): Response
    {
        $data = $request->all();
        $path = $data['path'];
        $this->mediaRepository->deleteMediaById((int) $id, $path);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }
    /**
     * Delete a media item.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteFolder(Request $request, $id): Response
    {
        $this->mediaRepository->deleteFolder((int) $id);
        return $this->renderResponse(['message' => 'Folder deleted successfully']);
    }
    /**
     * Delete a media item.
     *
     * @param Request $request
     * @param string $path
     * @return Response
     */
    public function deleteByPath(Request $request): Response
    {
        $path = $request->input('path');
        if(!$path){
            return $this->renderError(422, 'Path is required');
        }
        $this->mediaRepository->deleteMediaByPath($path);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }

    public function getFolders(Request $request): Response
    {
        $folders = $this->mediaRepository->getFolders();
        return $this->renderResponse($folders);
    }
    
    public function getSubFolders(Request $request, $id): Response
    {
        $sub_folder_name = $request->query('sub') ?? '';
        $folders = $this->mediaRepository->getSubFolders((int)$id, $sub_folder_name);
        return $this->renderResponse($folders);
    }

    public function getFiles(Request $request, $id = null): Response
    {
        $folderId = $request->input('folder_id', $id);
        
        if ($folderId) {
            $files = $this->mediaRepository->getFilesByFolderId((int)$folderId);
        } else {
            $files = $this->mediaRepository->getAllFiles();
        }
        
        return $this->renderResponse($files);
    }

    public function createFolder(Request $request): Response
    {
        try {
            // return $this->renderResponse("you hit create folder");
            $folder = $request->input('folder');
            if(!isset($folder['name'])){
                return $this->renderError(422, 'Name is required');
            }
            $newFolderPath = $folder['breadcrumb'] ?? 'media';
            $newFolderPath .= '/' . $folder['name'];

            $folderData = [
                'file' => json_encode(['path' => $newFolderPath,'objectURL' => $newFolderPath,'name' => $folder['name'] ?? 'New Folder']),
                'type' => 'folder',
                'meta' => $folder['name'] ?? 'New Folder',
                'parent_id' => $folder['parent_id'] ?? 0,
                'name' => $folder['name'] ?? 'New Folder',
                'path' => $newFolderPath,
                'folder_id' => $folder['folder_id'] ?? 0,
            ];
            // Create the folder
            $createdFolder = $this->mediaRepository->createFolder($folderData);
            
            if (!$createdFolder) {
                return $this->renderError(500, 'Failed to create folder');
            }
            
            return $this->renderResponse($createdFolder);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create folder: ' . $e->getMessage());
        }
    }

    public function getCategories(Request $request): Response
    {
        $categories = $this->mediaRepository->getCategories();
        return $this->renderResponse($categories);
    }


    public function mediaWayPoint(Request $request,$id): Response
    {
        $wayPoints = $this->mediaRepository->getMediaById($id);
        return $this->renderResponse($wayPoints);
    }
} 