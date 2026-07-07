<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\PostTag;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\PostTagDataValidation;
use League\Csv\Reader;
use App\Core\Models\Localisation\Language;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;

class PostTagRepository extends BaseRepository implements PostTagRepositoryInterface
{
    private Language $language;
    public function __construct(PDO $db, Language $language)
    {
        parent::__construct($db, 'post_tag', PostTag::class);
        $this->language = $language;
        $this->language->setDb($db);
    }

    public function get(?int $posttagId = null, ?string $slug = null, array $options = []): ?PostTag
    {
        $query = $this->model;

        // Load relationships
        // $query->with(['admin', 'postContent']);

        if ($posttagId) {
            $query->where('post_tag_id', '=', $posttagId);
        } else {
            return null;
        }

        // Apply language filter
        // if (!empty($options['language_id'])) {
        //     $query->where('postContent.language_id', '=', $options['language_id']);
        // }

        return $query->first();
    }
    public function insertPostTagImages(array $data, int $posttagId): bool
    {
        $imageData = [];
        $config = app('config');
        $imageServer = $config['APP_URL'];
        foreach($data as $image){
            $img = [];
            $img['post_tag_id'] = $posttagId;
            $img['image_link'] = $image['image'];
            $img['image'] = json_encode([
                'name' => $image['name'],
                'objectURL' => $imageServer.$image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => ROOT_DIR.PUBLIC_PATH.$image['image'],
                'status' => $image['status']
            ]);
            $img['sort_order'] = 0;
            $img['status'] = json_encode($image['status']);
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }
        if(count($imageData)){
            $this->db->beginTransaction();
            // $this->postTagImage->insert($imageData);
            $this->db->commit();
        }
        return true;
    }
    // public function deletePostImage(int $post_image_id): bool
    // {
        
    //     return $this->postImage->delete($post_image_id);
    // }

    public function getAllPostTags(): array
    {
        $data = $this->model
            ->select(['post_tag_id', 'name', 'slug', 'description', 'image', 'status','created_at'])
            ->whereNull('post_tag.deleted_at')
            ->findAll(false);
        return $data;
    }

    public function add(array $data): array
    {
        $this->model->clearQuery();

        // $response = [];
        $tag = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'status' => $data['status'],
            'image' => $data['image'] ?? null,
            'post_id' => $data['post_id'] ?? 1,
        ];

        try {
            $this->db->beginTransaction();
            unset($tag['image']);
            $obj = $this->model->create($tag);
            $insertedId = $obj->post_tag_id ?? null;
            $result = (array)$obj->data;
            $this->db->commit();

            return (array) $this->getPostTagById($insertedId);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create type: " . $e->getMessage());
        }

        // $this->model->clearQuery();
        // $postTag = $this->model->create($tag);
        // $posttagId = $data['post_tag_id'] = $postTag->post_tag_id ?? null;

        // $response = (array) $postTag->data;
        // $response['post_tag_id'] = $posttagId;
        // return $response;
    }

    public function findByName(string $name): ?PostTag
    {
        return $this->model
            ->where('post_tag.name', '=', $name)
            ->select(['post_tag.name', 'post_tag.slug', 'post_tag.description', 'post_tag.status', 'post_tag.post_id'])
            ->first();
    }
    
    public function getPostTagById($id)
    {
        $postTag = $this->model->where('post_tag_id', '=', $id)->first();
        // return $postTag->data;
        return $postTag ? (array) $postTag->data : [];
    }
    public function updatePostTags($id, $data, string $property = 'image'): array
    {
        try {
            $this->db->beginTransaction();

            $query = $this->model->where('post_tag_id', '=', $id)->first();

            if (isset($data['image']) && count($data['image'])) {
                $this->deleteFile($query->$property);
                $data[$property] = (is_string($data['files'])) ? $data['files'] : json_encode($data['files']);
                unset($data['files']);
            }

            if ($query) {
                unset($data['image']);
                $query->update($data);
            }
            $this->db->commit();
            return (array) $this->getPostTagById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update post tag: " . $e->getMessage());
        }
    }

    public function deleteFile($images)
    {
        if (isset($images) && !empty($images)) {
            $oldImages = is_string($images) ? json_decode($images, true) : $images;
            if (is_array($oldImages)) {
                foreach ($oldImages as $img) {
                    $oldPath = $_SERVER['DOCUMENT_ROOT'] . $img['objectURL'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath); // silent delete to avoid warnings
                    }
                }
            }
        }
    }

    public function updatePostTag(PostTag $postTag): PostTag
    { 
        $postTag = $this->model->find($postTag['post_tag_id']);
        unset($postTag['image']);
        $posttag = $postTag->update([$postTag]);

        // if(isset($images) && is_array($images)){
        //     if(count($images) > 0){
        //         $this->postImage->deleteWhere(['post_id' => $post->post_id]);
        //         $this->postImage->upsert($images, ['post_id', 'image_link']);
        //         $post->data->images = $images;
        //     }else{
        //         $postImages = $this->postImage->where('post_id', '=', $post->post_id)->select(['post_image_id'])->findAll();
        //         if(isset($postImages) && is_array($postImages)){
        //             $postImageIds = array_column($postImages, 'post_image_id');
        //             $this->postImage->deleteMultiple($postImageIds);
        //         }
        //         $post->data->images = [];
        //     }
        // }
        return $posttag;
    }

    public function findWithImages(int $id): ?array
    {
        // Get the specific post tag
        $postTag = $this->model->where('tag_id', '=', $id)->first();
        if (!$postTag) {
            return null;
        }

        $this->model->clearQuery();

        // Get post tag images for this specific post tag
        $postTagImages = $this->model
            ->select(['post_tag.tag_id', 'post_tag_image.image_id', 'post_tag_image.image', 'post_tag_image.name', 'post_tag_image.description', 'post_tag_image.size', 'post_tag_image.type', 'post_tag_image.created_at'])
            ->join('post_tag_image', 'post_tag_image.tag_id', '=', 'post_tag.tag_id')
            ->where('post_tag.tag_id', '=', $id)
            ->findAll();

        // Convert post tag object to array and add images
        $postTagData = $postTag->data;
        $postTagArray = (array) $postTagData;

        // Add images to post tag data
        $postTagArray['image'] = [];
        foreach ($postTagImages as $image) {
            $postTagArray['image'][] = [
                'id' => $image['image_id'],
                'media_id' => $image['image_id'],
                'image' => $image['image'],
                'name' => $image['name'],
                'description' => $image['description'],
                'size' => $image['size'],
                'type' => $image['type'],
                'objectURL' => $image['image'], // Assuming the image path is the object URL
                'status' => [
                    'name' => 'Uploaded',
                    'severity' => 'success'
                ],
                'created_at' => $image['created_at']
            ];
        }

        return $postTagArray;
    }

    public function findPostTags(): array
    {
        $postTags = $this->model->findAll();

        return $postTags;
    }

    public function searchPostTags(string $search): array
    {
        $result = $this->model
            ->select(['tag_id', 'name', 'slug', 'description', 'thumbnail'])
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhere('slug', 'like', '%' . $search . '%');

        $result = $result->findAll();
        return $result;
    }

    public function deletePostTag(int $post_tag_id): ?PostTag
    {
        $postTag = $this->model->where('post_tag_id', '=', $post_tag_id)->first();
        if ($postTag) {
            $postTag->update(['deleted_at' => date('Y-m-d H:i:s')]);
            return $postTag;
        }
        return null;
    }

    // import data
    public function importPostTags(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();

        $validData = [
            'post_tag_data' => [],
        ];
        $showFrontendValidData = ['post_tag_data' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['post_tag_id','name','slug', 'description', 'status', 'image', 'post_id'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'post_tag_id','name');
        $existingGroupIds = array_values($existingGroupMap);
      
        $existingDataMaps = [
            'postTagMap' => $existingGroupMap,
            'postTagIds' => $existingGroupIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                if(empty($record['slug'])){
                    $record['slug'] = $this->createSlug($record['name']);
                    $record['status'] = 1;
                }

                $mediaPaths = [
                    'image_path' => '/media/post-tag/image/',
                ];
                $validator = new PostTagDataValidation($record, $mediaPaths, $existingDataMaps);
                $validated = $validator->validate();

                // If validation fails, store record and error info in $invalid
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getPostTagUniqueIdentifier();

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if($validated->isExistingData){
                    $existingData[] = (array) $validated->post_tag;
                    $showFrontendExistingData[] = $record;
                }else{
                    $validData['post_tag_data'][] = (array) $validated->post_tag;
                    $contentData = (array) $validated->post_tag;
                    $showFrontendValidData['post_tag_data'][] = $contentData;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        // $result = $this->attributeGroupAndContentInsertorUpdate($validData, $languageMap);
        try{
            $this->db->beginTransaction();
            if(count($existingData) > 0){
                $this->model->upsert($existingData, ['post_tag_id']);
            }
            if(count($validData['post_tag_data']) > 0){
                $this->model->upsert($validData['post_tag_data'], ['name']);
                $postTagNames = array_column($validData['post_tag_data'], 'name');
                $this->model->clearQuery();
                $this->model->softDelete(false);

                // next day 
                $postTagData = $this->model->whereIn('name', $postTagNames)->select(['post_tag_id', 'name','description','image','post_id'])->findAll(false);
                $postTagData = array_column($postTagData, 'post_tag_id','name');
            }
            if(count($validData['post_tag_data']) > 0){
                foreach($validData['post_tag_data'] as &$content){
                    $content['post_tag_id'] = $postTagData[$content['name']];
                    // unset($content['code']);
                }
                $this->model->upsert($validData['post_tag_data'], ['name', 'post_id']);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update post tag groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['post_tag_data']),
            'valid_data' => $showFrontendValidData['post_tag_data'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'postTags' => [
                'inserted_count' => count($validData['post_tag_data']),
                'valid_data' => $validData['post_tag_data']
            ],
            'post_tags' => [
                'inserted_count' => count($showFrontendValidData['post_tag_data']),
                'valid_data' => $showFrontendValidData['post_tag_data']
            ],
            'invalid_data' => $invalid,
          
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['post_tag_data']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'posttag_processed' => count($validData['post_tag_data']),
                'content_records_created' => $validData['post_tag_data'],
                'errors' => count($invalid),
            ],
            
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['language_id'] = 1;
        $defaultFields['post_tag_id'] = null;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['post_tag_id']) && $record['post_tag_id'] ? $record : array_merge($defaultFields, $record);
    }

    private function createSlug($string) {
        // Convert to lowercase
        $slug = strtolower($string);
        
        // Remove any non-alphanumeric characters except spaces
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Replace multiple spaces or hyphens with a single hyphen
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Trim hyphens from the beginning and end
        $slug = trim($slug, '-');
        
        return $slug;
    } 
}
