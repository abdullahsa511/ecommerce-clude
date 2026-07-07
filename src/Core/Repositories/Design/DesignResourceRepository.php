<?php

declare(strict_types=1);

namespace App\Core\Repositories\Design;

use App\Core\Models\Design\DesignResource;
use App\Core\Models\Design\DesignResourceDocument;
use App\Core\Models\Media\Media;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\DesignResourceDataValidation;
use App\Core\Models\Project\Project;
use App\Core\Models\Project\ProjectImage;
use App\Core\Validation\DesignResourceDocumentDataValidation;
use App\Core\Validation\MediaDataValidation;
use App\Core\Models\Design\ResourceData;
use App\Core\Models\Product\ProductResource;
use App\Core\Models\Design\ResourceImageData;
use App\Core\Models\Design\GlobalSearchData;
use App\Core\Models\Showroom\Showroom;
use App\Core\Models\Post\Post;
use App\Core\Models\Post\PostImage;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductImage;
use App\Core\Models\PostCategory\Taxonomy;
use App\Core\Models\Product\ProductCertificate;
use App\Core\Models\Product\ProductToTaxonomyItem;
use App\Core\Models\Showroom\ProjectSection;
use App\Core\Models\Showroom\ProjectSectionImage;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\Search\PopularSearch;
use Exception;
use PDO;
use League\Csv\Reader;
use function App\Core\System\utils\env;


class DesignResourceRepository extends BaseRepository implements DesignResourceRepositoryInterface
{
    private Product $product;
    private Project $project;
    private ProductToTaxonomyItem $productToTaxonomyItem;
    private Showroom $showroom;
    private Taxonomy $taxonomy;
    private PostImage $postImage;
    private ProductImage $productImage;
    private ProjectImage $projectImage;
    private DesignResourceDocument $designResourceDocument;
    private Media $media;
    private Post $post;
    private ProjectSection $projectSection;
    private ProductVariant $productVariant;
    private ProjectSectionImage $projectSectionImage;
    private ProductResource $productResource;
    // popular search
    private PopularSearch $popularSearch;
    private ProductCertificate $productCertificate;


    public function __construct(
            PDO $db, 
            Product $product, 
            Taxonomy $taxonomy, 
            Project $project, 
            ProductToTaxonomyItem $productToTaxonomyItem,
            Showroom $showroom,
            PostImage $postImage,
            ProductImage $productImage,
            ProjectImage $projectImage, 
            DesignResourceDocument $designResourceDocument, 
            Media $media,
            Post $post,
            ProjectSection $projectSection,
            ProductVariant $productVariant,
            ProjectSectionImage $projectSectionImage,
            ProductResource $productResource,
            PopularSearch $popularSearch,
            ProductCertificate $productCertificate
        )
    {
        parent::__construct($db, 'design_resource', DesignResource::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->project = $project;
        $this->project->setDb($db);
        $this->productToTaxonomyItem = $productToTaxonomyItem;
        $this->productToTaxonomyItem->setDb($db);
        $this->showroom = $showroom;
        $this->showroom->setDb($db);

        $this->postImage = $postImage;
        $this->postImage->setDb($db);
        $this->productImage = $productImage;
        $this->productImage->setDb($db);
        $this->projectImage = $projectImage;
        $this->projectImage->setDb($db);
        $this->designResourceDocument = $designResourceDocument;
        $this->designResourceDocument->setDb($db);
        $this->media = $media;
        $this->media->setDb($db);
        $this->taxonomy = $taxonomy;
        $this->taxonomy->setDb($db);
        $this->post= $post;
        $this->post->setDb($db);
        $this->projectSection = $projectSection;
        $this->projectSection->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->projectSectionImage = $projectSectionImage;
        $this->projectSectionImage->setDb($db);
        $this->popularSearch = $popularSearch;
        $this->popularSearch->setDb($db);
        $this->productResource = $productResource;
        $this->productResource->setDb($db);
        $this->productCertificate = $productCertificate;
        $this->productCertificate->setDb($db);
    }


    public function insertDesignResources(array $data): bool
    {
        $designResources = $data['designResources'];
        $this->model->insert($designResources);
        return true;
    }


    public function getDesignResourceMainComponentData(array $param)
    {
        $model = 'design_resource';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model
                ->select([
                    'design_resource_id',
                    'title',
                    'img as image',
                    'description',
                    'resource_type'
                ]);

            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }

            $query->orderBy('design_resource_id', 'ASC');
            $results = $query->findAll();

            $finalResults = [];
            foreach ($results as $result) {
                // Process image data
                $imageData = json_decode($result['image'] ?? '{}', true);
                $imageUrl = $imageData['url'] ?? '/img/design-resources/default.png';

                $finalResults[] = [
                    'img' => $imageUrl,
                    'title' => $result['title'] ?? 'Untitled Resource',
                    'description' => $result['description'] ?? 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.'
                ];
            }

            return $finalResults;
        }
        return [];
    }

    public function getFeaturedMaterialSlider(array $param)
    {
        $model = 'design_resource';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            $query
                ->where('design_resource.resource_type', '=', 'finishes')
                ->select($param['fields'])
                ->orderBy('design_resource.design_resource_id', 'ASC')
                ->limit($param['item_count']);

            $results = $query->findAll(false);
            $items = [];

            foreach ($results as $index => $result) {
                $imageData = json_decode($result['img'] ?? '[]', true);
                if (empty($imageData) || !isset($imageData[0]['objectURL'])) continue;
                $imageUrl = $imageData[0]['objectURL'];


                $items[] = [
                    'description' => $result['description'] ?? 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                    'image' => $imageUrl,
                    'category' => $result['category'] ?? 'Finish',
                    'name' => $result['title'] ?? 'ABBEY'
                ];
            }

            return [
                // 'section_title' => 'Featured Materials',
                // 'section_subtitle' => "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque",
                'section_link_text' => 'View All Materials',
                'section_link' => env('APP_URL').'/account/resources/finishes',
                'items' => $items
            ];
        }
        return [
            // 'section_title' => 'Featured Materials',
            // 'section_subtitle' => "Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque",
            'section_link_text' => 'View All Materials',
            'section_link' => env('APP_URL').'/account/resources/finishes',
            'items' => []
        ];
    }

    public function getFeaturedMaterialSliderComponentData(array $param)
    {
        $model = 'design_resource';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model
                ->join('media', 'media.media_id', '=', 'design_resource.media_id')
                ->whereIn('design_resource.resource_type', ['finishes', 'textiles'])
                ->select([
                    'design_resource.design_resource_id',
                    'design_resource.title as name',
                    'design_resource.img as image',
                    'design_resource.description',
                    'design_resource.resource_type',
                    'media.url as media_url',
                    'media.alt_text as media_alt'
                ]);

            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }

            // Filter for featured materials if needed
            if (isset($param['is_featured']) && $param['is_featured'] == true) {
                $query->where('design_resource.is_featured', '=', 1);
            }

            $query->orderBy('design_resource.design_resource_id', 'ASC');
            $results = $query->findAll();

            $finalResults = [];
            foreach ($results as $result) {
                // Process image data - prioritize media table URL, fallback to JSON image data
                $imageUrl = $result['media_url'] ?? '/img/project-detail/material-default.png';

                // If no media URL, try to get from JSON image data
                if (empty($imageUrl) || $imageUrl === '/img/project-detail/material-default.png') {
                    $imageData = json_decode($result['image'] ?? '{}', true);
                    $imageUrl = $imageData['url'] ?? '/img/project-detail/material-default.png';
                }

                // Map type to category name
                $category = ucfirst($result['type'] ?? 'Material');

                $finalResults[] = [
                    'image' => $imageUrl,
                    'category' => $category,
                    'name' => $result['name'] ?? 'Untitled Material',
                    'description' => $result['description'] ?? 'Lorem Ipsum'
                ];
            }

            return $finalResults;
        }
        return [];
    }

    public function importDesignResources(string $csv_file, string $resource_type = 'documents'): array
    {
        return [];
    }

    private function prepareMediafiles(array &$validMedia, array &$validResourceDocuments, array $record, array &$invalidMedia, int $offset, &$designResourceImgMap, array &$invalid): bool
    {
        $path = '';
        match ($record['resource_type']) {
            'documents' => $path = '/media/design-resource/documents/',
            'models' => $path = '/media/design-resource/models/',
            'finishes' => $path = '/media/design-resource/finishes/',
            'textiles' => $path = '/media/design-resource/textiles/',
        };
        if ($record['resource_type'] == 'models' && isset($record['subfolder'])) {
            $path = $path . $record['subfolder'] . '/';
        }

        if (in_array($record['resource_type'], ['documents', 'models'])) {
            for ($i = 1; $i <= 5; $i++) {
                $file_name = 'file_' . $i;
                $file_format = 'file_format_' . $i;
                if (
                    !isset($record[$file_name]) || empty($record[$file_name]) ||
                    !isset($record[$file_format]) || empty($record[$file_format])
                ) {
                    continue;
                }
                if ($record['resource_type'] == 'models' && isset($record['subfolder'])) {
                    $path = $path . $record['subfolder'] . '/';
                }

                $filePath = $path . $record[$file_name];
                if (
                    isset($record[$file_name]) && !empty($record[$file_name])
                    && isset($record[$file_format]) && !empty($record[$file_format])
                ) {
                    $data = [
                        'file' => $record[$file_name],
                        'name' => $record[$file_name],
                        'type' => $record[$file_format],
                        'path' => $filePath,
                    ];
                }
                unset($record[$file_name]);
                unset($record[$file_format]);
                $validation = new MediaDataValidation($data, ['name', 'type', 'path']);
                $validationResult = $validation->validate();
                if ($validationResult === false) {
                    $invalidMedia[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $validation->getErrors()
                    ];
                    continue;
                }
                $validMedia[$filePath] = $validationResult->toArray();
                $designResourceDocumentValidation = new DesignResourceDocumentDataValidation($validMedia[$filePath], ['name', 'type', 'path']);
                $validResourceDocuments[$record['title']][$filePath] = $designResourceDocumentValidation->toArray();
            }
        }

        $result = $this->prepareDesignResourceImg($validMedia, $designResourceImgMap, $record, 'img', $path, $invalid, $offset);
        if ($result === false) {
            return false;
        }
        $result = $this->prepareDesignResourceImg($validMedia, $designResourceImgMap, $record, 'img2', $path, $invalid, $offset);
        if ($result === false) {
            return false;
        }
        return true;
    }

    private function prepareDesignResourceImg(array &$validMedia, array &$designResourceImgMap, array $record, string $fieldName, string $path, array &$invalid, int $offset): bool
    {
        if (isset($record[$fieldName]) && !empty($record[$fieldName])) {
            $p = $path . $record[$fieldName];
            $data = [
                'file' => $record[$fieldName],
                'name' => $record[$fieldName],
                'type' => 'image/jpeg', // default type is image/jpeg  // pathinfo($record[$fieldName], PATHINFO_EXTENSION)
                'path' => $p,
            ];
            $validation = new MediaDataValidation($data, ['name', 'type', 'path']);
            $validationResult = $validation->validate();
            if ($validationResult === false) {
                $invalid[] = [
                    'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                    'data' => $record,
                    'errors' => $validation->getErrors()
                ];
                return false;
            }
            $validMedia[$p] = $validationResult->toArray();
            if (!isset($designResourceImgMap[$record['title']])) {
                $designResourceImgMap[$record['title']] = $p;
            }
        }
        return true;
    }

    public function importResources(string $csv_file, string $resource_type): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = $this->getRequiredFields();
        $records = $reader->getRecords();

        $existingDataMap = [];
        $existingData = $this->model->select(['design_resource_id', 'title'])->where('resource_type', '=', $resource_type)->findAll(false);
        if (!empty($existingData)) {
            $existingDataMap['designResourceIds'] = array_column($existingData, 'design_resource_id', 'title');
            $existingDataMap['designResourceTitles'] = array_column($existingData, 'design_resource_id', 'title');
        }

        $productDataMap = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productIdsMap = array_column($productDataMap, 'product_id', 'product_code');

        $valid = [];
        $invalidMedia = [];
        // $validResourceDocuments = [];
        $processedIdentifiers = [];
        $invalid = [];
        $updated = [];
        $designResourceImgMap = [];
        //1. $validResource
        //2. $validProductResource
        //3. $validResourceDocument
        //4. $validMedia
        $validResources = [];
        $validProductResources = [];
        $validResourceDocuments = [];
        $validMedia = [];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $record['resource_type'] = $resource_type;
                // $result = $this->prepareMediafiles($validMedia, $validResourceDocuments, $record, $invalidMedia, $offset, $designResourceImgMap, $invalid);
                // if ($result === false) {
                //     continue;
                // }
                $validation = new DesignResourceDataValidation($record, $resource_type, $requiredFields, [], $productIdsMap);
                $validationResult = $validation->validate();

                if ($validationResult === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $validation->getErrors()
                    ];
                    continue;
                }

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $validation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                // Check here if $validationResult->isExistingData true then 
                //This is only for showing in the frontend
                if ($validationResult->isExistingData) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }
                //Design Resource Records Data
                if(!isset($validResources[$validationResult->design_resource->title])){
                    $validResources[$validationResult->design_resource->title] = (array) $validationResult->design_resource;
                }
                //Media Records Data
                $validMedia[] = (array) $validationResult->media;

                // Documents/models only: resource_document and product_resource are populated in validation for these types.
                if (in_array($resource_type, ['documents', 'models'], true)) {
                    //Design Resource Document Records Data
                    $documentUniqueIdentifier = $validationResult->design_resource->title . '-' . $validationResult->design_resource->resource_type . '_' . $validationResult->resource_document->url;
                    if (!isset($validResourceDocuments[$documentUniqueIdentifier])) {
                        $validResourceDocuments[$documentUniqueIdentifier] = (array) $validationResult->resource_document;
                    }

                    //Product Resource Records Data
                    if (!isset($validProductResources[$validationResult->design_resource->title])) {
                        $validProductResources[$validationResult->design_resource->title] = (array) $validationResult->product_resource;
                    }
                }

                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                error_log("Error processing row " . ($offset + 2) . ": " . $e->getMessage());
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $insertedCount = 0;
        // if (!empty($valid)) {
            try {
                $this->db->beginTransaction();
                $mediaData = array_values($validMedia);
                // upsert media table 
                $this->media->upsert($mediaData, ['path']);
                $paths = array_column($validMedia, 'path');
                $mediaIdsMap = $this->media->whereIn('path', $paths)->select(['path', 'media_id'])->limit(0)->findAll();
                $mediaIdsMap = array_column($mediaIdsMap, 'media_id', 'path');

                $validResourcesData = [];
                foreach ($validResources as $title => $resource) {
                    if(isset($resource['media_path'])){
                        $resource['media_id'] =  (isset($mediaIdsMap[$resource['media_path']])) ?  $mediaIdsMap[$resource['media_path']] : null;
                        unset($resource['media_path']);
                    }
                    $validResourcesData[] = $resource;
                }
                // upsert design resource table
                $this->model->upsert($validResourcesData, ['title', 'resource_type', 'brand']);

                // upsert design resource document table
                if (in_array($resource_type, ['documents', 'models'])) {
                    $titles = array_column($validResourcesData, 'title');
                    $this->model->clearQuery();
                    $designResourceIds = $this->model->select(['design_resource_id', 'title'])->whereIn('title', $titles)->where('resource_type', '=', $resource_type)->limit(0)->findAll(false);
                    $designResourceIdsMap = array_column($designResourceIds, 'design_resource_id', 'title');

                    $validProductResourcesData = [];
                    foreach($validProductResources as $productResource){
                        if(isset($designResourceIdsMap[$productResource['resource_title']])){
                            $productResource['design_resource_id'] = $designResourceIdsMap[$productResource['resource_title']];
                            unset($productResource['resource_title']);
                            $validProductResourcesData[] = $productResource;
                        }
                    }
                    $this->productResource->upsert($validProductResourcesData, ['product_id', 'design_resource_id']);

                    $resourceDocs = [];
                    foreach ($validResourceDocuments as &$document) {
                        if (isset($designResourceIdsMap[$document['design_resource_title']])) {
                            $document['design_resource_id'] = $designResourceIdsMap[$document['design_resource_title']];
                            $document['media_id'] =  (isset($mediaIdsMap[$document['url']])) ?  $mediaIdsMap[$document['url']] : null;
                            unset($document['design_resource_title']);
                            $resourceDocs[] = $document;
                        }
                    }
                    $this->designResourceDocument->upsert($resourceDocs, ['design_resource_id', 'url']);
                }

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert finishes: " . $e->getMessage());
            }
        // }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validResources),
            'valid_product_records' => count($validProductResources),
            'valid_resource_documents_records' => count($validResourceDocuments),
            'valid_media_records' => count($validMedia),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => array_values($validResources),
            'valid_product_resources' => $validProductResources,
            'valid_resource_documents' => $validResourceDocuments,
            'valid_media' => $validMedia,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importResources_abdullah13052026(string $csv_file, string $resource_type): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = $this->getRequiredFields();
        $records = $reader->getRecords();

        $existingDataMap = [];
        $existingData = $this->model->select(['design_resource_id', 'title'])->where('resource_type', '=', $resource_type)->findAll(false);
        if (!empty($existingData)) {
            $existingDataMap['designResourceIds'] = array_column($existingData, 'design_resource_id', 'title');
            $existingDataMap['designResourceTitles'] = array_column($existingData, 'design_resource_id', 'title');
        }

        $productDataMap = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productIdsMap = array_column($productDataMap, 'product_id', 'product_code');

        $valid = [];
        $invalidMedia = [];
        // $validResourceDocuments = [];
        $processedIdentifiers = [];
        $invalid = [];
        $updated = [];
        $designResourceImgMap = [];
        //1. $validResource
        //2. $validProductResource
        //3. $validResourceDocument
        //4. $validMedia
        $validResources = [];
        $validProductResources = [];
        $validResourceDocuments = [];
        $validMedia = [];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $record['resource_type'] = $resource_type;
                // $result = $this->prepareMediafiles($validMedia, $validResourceDocuments, $record, $invalidMedia, $offset, $designResourceImgMap, $invalid);
                // if ($result === false) {
                //     continue;
                // }
                $validation = new DesignResourceDataValidation($record, $resource_type, $requiredFields, [], $productIdsMap);
                $validationResult = $validation->validate();

                if ($validationResult === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $validation->getErrors()
                    ];
                    continue;
                }

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $validation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                // Check here if $validationResult->isExistingData true then 
                //This is only for showing in the frontend
                if ($validationResult->isExistingData) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }
                //Design Resource Records Data
                if(!isset($validResources[$validationResult->design_resource->title])){
                    $validResources[$validationResult->design_resource->title] = (array) $validationResult->design_resource;
                }
                //Media Records Data
                $validMedia[] = (array) $validationResult->media;

                //Design Resource Document Records Data
                $documentUniqueIdentifier = $validationResult->design_resource->title . '-' . $validationResult->design_resource->resource_type . '_' . $validationResult->resource_document->url;
                if(!isset($validResourceDocuments[$documentUniqueIdentifier])){
                    $validResourceDocuments[$documentUniqueIdentifier] = (array) $validationResult->resource_document;
                }

                //Product Resource Records Data
                if(!isset($validProductResources[$validationResult->design_resource->title])){
                    $validProductResources[$validationResult->design_resource->title] = (array) $validationResult->product_resource;
                }
                
                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                error_log("Error processing row " . ($offset + 2) . ": " . $e->getMessage());
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $insertedCount = 0;
        // if (!empty($valid)) {
            try {
                $this->db->beginTransaction();
                $mediaData = array_values($validMedia);
                // upsert media table 
                $this->media->upsert($mediaData, ['path']);
                $paths = array_column($validMedia, 'path');
                $mediaIdsMap = $this->media->whereIn('path', $paths)->select(['path', 'media_id'])->limit(0)->findAll();
                $mediaIdsMap = array_column($mediaIdsMap, 'media_id', 'path');

                $validResourcesData = [];
                foreach ($validResources as $title => $resource) {
                    if(isset($resource['media_path'])){
                        $resource['media_id'] =  (isset($mediaIdsMap[$resource['media_path']])) ?  $mediaIdsMap[$resource['media_path']] : null;
                        unset($resource['media_path']);
                    }
                    $validResourcesData[] = $resource;
                }
                // upsert design resource table
                $this->model->upsert($validResourcesData, ['title', 'resource_type']);

                // upsert design resource document table
                if (in_array($resource_type, ['documents', 'models'])) {
                    $titles = array_column($validResourcesData, 'title');
                    $this->model->clearQuery();
                    $designResourceIds = $this->model->select(['design_resource_id', 'title'])->whereIn('title', $titles)->where('resource_type', '=', $resource_type)->limit(0)->findAll(false);
                    $designResourceIdsMap = array_column($designResourceIds, 'design_resource_id', 'title');

                    $validProductResourcesData = [];
                    foreach($validProductResources as $productResource){
                        if(isset($designResourceIdsMap[$productResource['resource_title']])){
                            $productResource['design_resource_id'] = $designResourceIdsMap[$productResource['resource_title']];
                            unset($productResource['resource_title']);
                            $validProductResourcesData[] = $productResource;
                        }
                    }
                    $this->productResource->upsert($validProductResourcesData, ['product_id', 'design_resource_id']);

                    $resourceDocs = [];
                    foreach ($validResourceDocuments as &$document) {
                        if (isset($designResourceIdsMap[$document['design_resource_title']])) {
                            $document['design_resource_id'] = $designResourceIdsMap[$document['design_resource_title']];
                            $document['media_id'] =  (isset($mediaIdsMap[$document['url']])) ?  $mediaIdsMap[$document['url']] : null;
                            unset($document['design_resource_title']);
                            $resourceDocs[] = $document;
                        }
                    }
                    $this->designResourceDocument->upsert($resourceDocs, ['design_resource_id', 'url']);
                }

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert finishes: " . $e->getMessage());
            }
        // }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validResources),
            'valid_product_records' => count($validProductResources),
            'valid_resource_documents_records' => count($validResourceDocuments),
            'valid_media_records' => count($validMedia),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => array_values($validResources),
            'valid_product_resources' => $validProductResources,
            'valid_resource_documents' => $validResourceDocuments,
            'valid_media' => $validMedia,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importResources_backup(string $csv_file, string $resource_type): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = $this->getRequiredFields();
        $records = $reader->getRecords();

        $existingDataMap = [];
        $existingData = $this->model->select(['design_resource_id', 'title'])->where('resource_type', '=', $resource_type)->findAll(false);
        if (!empty($existingData)) {
            $existingDataMap['designResourceIds'] = array_column($existingData, 'design_resource_id', 'title');
            $existingDataMap['designResourceTitles'] = array_column($existingData, 'design_resource_id', 'title');
        }

        $valid = [];
        $validMedia = [];
        $invalidMedia = [];
        $validResourceDocuments = [];
        $processedIdentifiers = [];
        $invalid = [];
        $updated = [];
        $designResourceImgMap = [];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $record['resource_type'] = $resource_type;
                $result = $this->prepareMediafiles($validMedia, $validResourceDocuments, $record, $invalidMedia, $offset, $designResourceImgMap, $invalid);
                if ($result === false) {
                    continue;
                }
                $validation = new DesignResourceDataValidation($record, $resource_type, $requiredFields, [], $existingDataMap);
                $validationResult = $validation->validate();

                if ($validationResult === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $validation->getErrors()
                    ];
                    continue;
                }

                // Check for duplicates using unique identifier
                $uniqueIdentifier = $validation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }

                // Check here if $validationResult->isExistingData true then 
                //This is only for showing in the frontend
                if ($validationResult->isExistingData) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                } else {
                    $valid[] = (array) $validationResult->design_resource;
                }
                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                error_log("Error processing row " . ($offset + 2) . ": " . $e->getMessage());
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $insertedCount = 0;
        if (!empty($valid)) {
            try {
                $this->db->beginTransaction();
                $mediaData = array_values($validMedia);
                // upsert media table 
                $this->media->upsert($mediaData, ['path']);
                $paths = array_keys($validMedia);
                $mediaIdsMap = $this->media->whereIn('path', $paths)->select(['path', 'media_id'])->limit(0)->findAll();
                $mediaIdsMap = array_column($mediaIdsMap, 'media_id', 'path');
                foreach ($valid as $key => &$resource) {
                    if (isset($designResourceImgMap[$resource['title']])) {
                        $path = $designResourceImgMap[$resource['title']];
                        $valid[$key]['media_id'] =  (isset($mediaIdsMap[$path])) ?  $mediaIdsMap[$path] : null;
                    } else {
                        $valid[$key]['media_id'] = null;
                    }
                }
                // upsert design resource table
                $this->model->upsert(array_values($valid), ['title', 'resource_type']);

                // upsert design resource document table
                if (in_array($resource_type, ['documents', 'models'])) {
                    $titles = array_column($valid, 'title');
                    $designResourceIds = $this->model->select(['design_resource_id', 'title'])->whereIn('title', $titles)->where('resource_type', '=', $resource_type)->limit(0)->findAll(false);
                    $designResourceIdsMap = array_column($designResourceIds, 'design_resource_id', 'title');

                    $resourceDocs = [];
                    foreach ($validResourceDocuments as $title => &$documents) {
                        if (isset($designResourceIdsMap[$title])) {
                            $designResourceId = $designResourceIdsMap[$title];
                            foreach ($documents as $rp => &$document) {
                                if (isset($mediaIdsMap[$rp]) && $mediaIdsMap[$rp] !== null) {
                                    $document['design_resource_id'] = $designResourceId;
                                    $document['media_id'] =  (isset($mediaIdsMap[$rp])) ?  $mediaIdsMap[$rp] : null;
                                    $resourceDocs[] = (array) $document;
                                }
                            }
                        }
                    }
                    $resourceDocs = array_values($resourceDocs);
                    $this->designResourceDocument->upsert($resourceDocs, ['design_resource_id', 'media_id']);
                }

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert finishes: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $insertedCount,
            'valid_data' => $valid,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['is_featured'] = 0;
        $defaultFields['tag'] = '';

        return $defaultFields;
    }

    private function getRequiredFields(): array
    {

        $designResource = [
            'title',
            'resource_type',
        ];
        $designResourceDocument = [
            'name',
            'format',
        ];

        return array_merge($designResource, $designResourceDocument);
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['design_resource_id']) && $record['design_resource_id'] ? $record : array_merge($defaultFields, $record);
    }

   

    //Resource Images is taking from project_image table
    public function getDesignResourceImagesPaginationsData(int $current_page, int $per_page)
    {
        $limit = $per_page;
        $start = $per_page * $current_page;

        $results = $this->projectImage->orderBy('project_image.project_image_id', 'DESC')
            ->limit($limit)
            ->offset($start)
            ->findAll();

        $total = $this->projectImage->countAll();

        $items = [];
        foreach ($results as $index => $result) {
            // $img = $result;
            $imageData = json_decode($result['image'] ?? '{}', true);

            $imageData = isset($imageData['objectURL']) ? $imageData : $imageData[0] ?? [];
            if (!isset($imageData['objectURL']))
                continue;
            $imageUrl = $imageData['objectURL'];
            $gridAreaPattern = ['8-4', '8-6', '8-6', '8-4', '16-6'];
            $i = ($index % count($gridAreaPattern));
            $gridArea = $i;

            $items[] = [
                'type' => 'th-masonry-img-item-' . $gridArea,
                'dataSrc' => $imageUrl,
                'dataBgSrc' => $imageUrl,
                'title' => $result['project_image_id'] ?? 'Lorem ipsum dolor ',
                'gridArea' => $gridArea,
                'class' => 'th-masonry-img-item th-masonry-img-item-' . $gridArea
            ];
        }


        return [
            'list' => $items,
            'total' => $total,
            'current_page' => $current_page,
            'per_page' => $per_page,
        ];
    }


    // =================================== tab content data =================================

    public function getDesignResourceImagesComponentData(array $params)
    {
        
        $results = $this->prepareQueryImages($params);
        
        if(isset($results['items']) && !empty($results['items'])){
            $results['items'] = array_map(function($r, $i) {
                $data = new ResourceImageData($r, $i);
                return $data->toArray();
            }, $results['items'], array_keys($results['items']));
        }
        $results['total_result'] = 'Images: ' . count($results['items']) . ' Results';
        return $results;
    }

    private function prepareQueryImages(array $params): array
    {
        $query = $this->media->groupBy('media.path');
        $associations = ['product', 'project', 'showrooms', 'post'];
        if(isset($params['context']) && !empty($params['context']) && in_array($params['context'], $associations)){
            $associations = [$params['context']];
        }
        $conditions =[];
        foreach($associations as $association){
            switch($association){
                case 'product':
                    $query->join('product_image', 'product_image.media_id', '=', 'media.media_id');
                    $query->join('product', 'product.product_id', '=', 'product_image.product_id');
                    $query->join('product_content', 'product_content.product_id', '=', 'product.product_id');
                    $conditions[] = 'product_image.media_id IS NOT NULL';
                    $conditions[] = 'product.media_id IS NOT NULL';
                    $query->orderBy('ISNULL(product.product_id)', 'ASC');
                    $query->orderBy('product.product_id', 'ASC');
                    $query->select(['product.product_id', 'product.product_code', 'product_content.title as product_title', 'product_content.slug']);
                    break;
                case 'project':
                    $query->join('project_image', 'project_image.media_id', '=', 'media.media_id');
                    $query->join('project', 'project.project_id', '=', 'project_image.project_id');
                    $conditions[] = 'project_image.media_id IS NOT NULL';
                    $query->select(['project_image.project_id', 'project.name as project_name', 'project.name as path', 'project_image.image as file', 'project.slug']);
                    if(isset($params['context']) && $params['context'] === 'project'){
                        $query->orderBy('project.project_id', 'DESC');
                    }
                    break;
                case 'showrooms':
                    $query->join('project_section_images', 'project_section_images.media_id', '=', 'media.media_id');
                    $query->join('project_sections', 'project_sections.project_sections_id', '=', 'project_section_images.section_id');
                    $conditions[] = 'project_section_images.media_id IS NOT NULL';
                    $query->select(['project_section_images.section_id', 'project_sections.title']);
                    break;
                case 'post':
                    $query->join('post_image', 'post_image.media_id', '=', 'media.media_id');
                    $query->join('post_content', 'post_content.post_id', '=', 'post_image.post_id');
                    $conditions[] = 'post_image.media_id IS NOT NULL';
                    $query->select(['post_image.post_id', 'post_content.name as post_title']);
                    if(isset($params['context']) && $params['context'] === 'post'){
                        $query->orderBy('post_image.post_id', 'DESC');
                    }
                    break;
            }
        }
        if(count($conditions) > 0){
            $conditionsRaw = '(' . implode(' OR ', $conditions) . ')';
            $query->whereRaw($conditionsRaw);
        }

        // if((isset($params['category']) && !empty($params['category']))){ // old condition
        if(isset($params['context'])){
            switch($params['context']){
                case 'product':
                    $query->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id');
                    if(isset($params['category']) && !empty($params['category'])){
                        $query->where('product_to_taxonomy_item.taxonomy_item_id', '=', $params['category']);
                    }
                    
                    // if(isset($params['model_id']) && !empty($params['model_id'])){
                    //     $modelCond[] = 'product_image.product_id = '. $params['model_id'];
                    //     $modelCond[] = 'product.product_id = '. $params['model_id'];
                    //     $modelConditionsRaw = '(' . implode(' OR ', $modelCond) . ')';
                    //     $query->whereRaw($modelConditionsRaw);
                    // }
                    break;
                case 'project':
                    if(isset($params['model_id']) && !empty($params['model_id'])){
                       $modelCond[] = 'project_image.project_id = '. $params['model_id'];
                       $modelCond[] = 'project.name LIKE "%' . $params['model_name'] . '%"';
                       $modelConditionsRaw = '(' . implode(' OR ', $modelCond) . ')';
                       $query->whereRaw($modelConditionsRaw);
                    }
                    break;
                case 'showrooms':
                    if(isset($params['category']) && !empty($params['category'])){
                        $showroomId = $params['category'];
                        // $modelCond[] = 'project_section_images.section_id = '. $showroomId;
                        $modelCond[] = 'project_sections.showroom_id = '. $showroomId;
                        if(isset($params['model_name']) && !empty($params['model_name'])){
                            $modelCond[] = 'project_sections.title LIKE "%' . $params['model_name'] . '%"';
                        }
                        $modelConditionsRaw = '(' . implode(' OR ', $modelCond) . ')';
                        $query->whereRaw($modelConditionsRaw);
                    }
                    break;
                case 'post':
                    break;
                default:
                    break;
            }
        }
        $countQuery = clone $query;
        $countQuery->limit(0);
        // var_dump($countQuery->buildCountQuery());
        // exit;

        // item count
        if(isset($params['item_count']) && $params['item_count'] > 0){
            $query->limit($params['item_count'] * 1);
        }
        // pagination
        if (
            isset($params['per_page']) &&
            isset($params['current_page']) &&
            $params['current_page'] > 0
        ) {
            $offset = $params['offset'] ?? ($params['current_page'] - 1) * $params['per_page'];
            $limit = ($params['per_page']*$params['current_page'])-$offset;
    
            $query->offset($offset);
            $query->limit($limit);
        }
       
        if($params['context'] === 'showrooms'){
            $query->select([
                'project_section_images.section_id', 
                'project_section_images.image_link as path', 
                'project_sections.title as title', 
                // 'project_sections.image as file',
                'project_section_images.image as file'
            ]);
        }
        if($params['context'] === 'post' || $params['context'] === 'product' || empty($params['context'])){
            $query->select(['media.*']);
        }
        // var_dump($query->getQuery());
        // exit;
        // $query->where('product.product_id', '=', 1);
        $data = $query->findAll(false);
        $totalCount = $countQuery->countAll();
        $count = $params['per_page'] * $params['current_page'];

        if($count >= $totalCount){
            $offsetCount = $totalCount;
        }else{
            $offsetCount = $count;
        }

        $pagination = [
            'per_page' => $params['per_page'],
            'current_page' => $params['current_page'],
            'offset' => $offsetCount,
            'context' => $params['context']??null,
            'category' => $params['category']??null,
            'model_id' => $params['model_id']??null,
            'model_name' => $params['model_name']??null,
            'total' => $totalCount,
            'resource_type' => 'images'
        ];
        return ['items' => $data, 'pagination' => $pagination];
    }

    public function getDesignResourceModelsComponentData(array $param)
    {
        $data = $this->getDesignResourceComponentData($param);

        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (
                    isset($item['design_resource_documents'])
                    && is_array($item['design_resource_documents'])
                ) {
                    usort($item['design_resource_documents'], static function ($a, $b): int {
                        $nameA = is_array($a) ? ($a['name'] ?? '') : '';
                        $nameB = is_array($b) ? ($b['name'] ?? '') : '';
                        return strcasecmp((string) $nameA, (string) $nameB);
                    });
                }
            }
            unset($item);

            usort($data['items'], static function ($a, $b): int {
                $titleA = is_array($a) ? ($a['title'] ?? '') : '';
                $titleB = is_array($b) ? ($b['title'] ?? '') : '';
                return strcasecmp((string) $titleA, (string) $titleB);
            });
        }

        return $data;




    }

    public function getDesignResourceModelsComponentData_2(array $param)
    {
        $context = $param['context']??null;
        $category = $param['category']??0;
        $primary_key_id = $param['model_id']??0; // model id is the primary key id
        
        // get the table map from FilterTableMap
        // $modelMap = FilterTableMap::TABLE_MAP[$context];
        $results = $this->prepareQueryModels($context, (int) $primary_key_id, (int) $category, $param);
        
        if(isset($results['items']) && !empty($results['items'])){
            $items = array_map(function($r, $i) {
                $data = new ResourceFilterData($r, $i);
                return $data->toArray();
            }, $results['items'], array_keys($results['items']));

            return [
                'total_result' => 'Models: ' . count($items) . ' Results',
                'offset' => $results['offset']??0,
                'total' => $results['total'],
                'items' => $items
            ];
        }
        return [
            'total_result' => 'Images: 0 Results',
            'items' => []
        ];
    }

    private function prepareQueryResource(array $params): array
    {
            // echo '<pre>';
            // print_r($params);
            // echo '</pre>';
            // exit;
        $designResourceId = isset($params['design_resource_id']) ? (int)$params['design_resource_id'] : null;
        $context = $params['context'] ?? null;
        $this->model->clearQuery();
        $query = $this->model
        ->where('design_resource.resource_type', '=', $params['type']??'models')
        ->orderBy('design_resource.sort_order', 'ASC');
        // ->select($param['fields']);
        if(in_array($params['type'], ['images', 'models', 'documents'])){
            $query->join('product_resource', 'product_resource.design_resource_id', '=', 'design_resource.design_resource_id');
            $query->join('product', 'product.product_id', '=', 'product_resource.product_id');
            $query->where('product.status', '=', 1);
            $query->select(['design_resource.*', 'product.product_id', 'product.image_thumb as product_thumbnail']);
        }else{
            $query->select(['design_resource.*']);
        }

        // if design resource id is set, then filter by design resource id
        if ($designResourceId) {
            $query->where('design_resource.design_resource_id', '=', $designResourceId);
        }

        if ($context === 'brand') {
            if(isset($params['model_id']) && $params['model_id'] > 0){
                $query->where('design_resource.brand', 'LIKE', '%' . $params['model_id'] . '%');
            }else if(isset($params['searchValue']) && !empty($params['searchValue'])){
                $query->where('design_resource.brand', 'LIKE', '%' . $params['search_value'] . '%');
            }
        }

        // if context is product, then filter by product id
        if ($context === 'product' && $params['type'] === 'images') {
            $query->join('product_resource', 'product_resource.design_resource_id', '=', 'design_resource.design_resource_id');
            $query->join('product', 'product.product_id', '=', 'product_resource.product_id');
            $query->where('product.status', '=', 1);
            $query->whereNotNull('product_resource.product_id');
            if(isset($params['category']) && $params['category'] > 0){
                $query->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product_resource.product_id');
                $query->where('product_to_taxonomy_item.taxonomy_item_id', '=', $params['category']);
            }
            if(isset($params['model_id']) && $params['model_id'] > 0){
                $query->where('product_resource.product_id', '=', $params['model_id']);
            }else if(isset($params['search_value']) && !empty($params['search_value'])){
                $query->where('design_resource.title', 'LIKE', '%' . $params['search_value'] . '%');
            }
        }
        // else if(isset($params['category']) && $params['category']){
        //     if(isset($params['type']) && in_array($params['type'], ['finishes', 'textiles'])) {
        //         if(isset($params['context'])){
        //             $field = 'design_resource.'.$params['context'];
        //             $query->where($field, 'LIKE', '%' . $params['category'] . '%');
        //         }else{
        //             $query->where(function($q)use($params){
        //                 $q->where('design_resource.type', 'LIKE', '%' . $params['category'] . '%')
        //                 ->orWhere('design_resource.brand', 'LIKE', '%' . $params['category'] . '%');
        //             });
        //         }
        //     }

        // }


        // prepare count query
        $countQuery = clone $query;
        $countQuery->limit(0);
        // var_dump($countQuery->buildCountQuery());
        // exit;
        // item count
        if(isset($params['item_count']) && $params['item_count'] > 0){
            $query->limit($params['item_count'] * 1);
        }
        // pagination
        if (
            isset($params['per_page']) &&
            isset($params['current_page']) &&
            $params['current_page'] > 0
        ) {
            $offset = (int) ($params['offset'] ?? ($params['current_page'] - 1) * $params['per_page']);
            $limit = (int) ($params['per_page']*$params['current_page'])-$offset;
    
            $query->offset($offset);
            $query->limit($limit);
        }

        // execute query
        $query->with(['design_resource_documents']);
        
        if(in_array($params['type'], ['documents', 'models']) || ($context === 'product' && $params['type'] === 'images')){
            if(in_array($params['type'], ['documents', 'models'])){
                $query->join('product_content', 'product_content.product_id', '=', 'product.product_id');
                $query->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product_resource.product_id');
                $query->join('taxonomy_item', 'taxonomy_item.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id');
                $query->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id');
                $query->select(['design_resource.*', 'product.product_id', 'product.image_thumb as product_thumbnail, taxonomy_item_content.slug', 'taxonomy_item_content.taxonomy_item_id as category_id', 'product_content.slug as product_slug']);
                $query->groupBy('product_to_taxonomy_item.product_id');
            }else{
                $query->select(['design_resource.*', 'product.product_id', 'product.image_thumb as product_thumbnail']);
            }
        }else{
            $query->select(['design_resource.*']);
        }
        // var_dump($countQuery->buildCountQuery());
        // exit;
        $results = $query->findAll();

        foreach ($results as &$designResource) {
            $imageData =  isset($designResource['img']) ? json_decode($designResource['img'], true) : (isset($designResource['product_thumbnail']) ? json_decode($designResource['product_thumbnail'], true) : []);
            $imageUrl = '/img/design-resources/vira.png'; // Default image

            if (!empty($imageData) && isset($imageData[0]['objectURL'])) {
                $imageUrl = $imageData[0]['objectURL'];
            } elseif (!empty($imageData) && isset($imageData[0]['image'])) {
                $imageUrl = $imageData[0]['image'];
            }
            $designResource['image'] = $imageUrl;
            $designResource['design_resource_documents'] = $this->removeDuplicateDesignResourceDocuments($designResource['design_resource_documents']);
            // $designResource['design_resource_documents'] = json_decode($designResource['design_resource_documents'] ?? '[]', true);
        }
        $totalCount = $countQuery->countAll();
        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 0;
        $currentPage = isset($params['current_page']) ? (int) $params['current_page'] : 0;
        $count = $perPage * $currentPage;
        if($count >= $totalCount){
            $offsetCount = $totalCount;
        }else{
            $offsetCount = $count;
        }
        $pagination = [
            'per_page' => $params['per_page'] ?? null,
            'current_page' => $params['current_page'] ?? null,
            'offset' => $offsetCount,
            'context' => $context,
            'category' => $params['category']??null,
            'model_id' => $params['model_id']??null,
            'model_name' => $params['model_name']??null,
            'total' => $totalCount,
            'resource_type' => $params['type']
        ];
        return ['items' => $results, 'pagination' => $pagination];
    }

    /**
     * @param array<int, array<string, mixed>>|string|null $designResourceDocuments From JSON_ARRAYAGG, or pre-decoded rows
     */
    private function removeDuplicateDesignResourceDocuments(array|string|null $designResourceDocuments): array
    {
        // Dedupe by design_resource_document_id (same ID can repeat when JSON_ARRAYAGG runs
        // after joins — e.g. product_resource multiplies rows per design_resource).
        if (is_array($designResourceDocuments)) {
            $docs = $designResourceDocuments;
        } else {
            $docs = json_decode((string) ($designResourceDocuments ?? '[]'), true) ?: [];
        }
        $unique = [];
        foreach ($docs as $doc) {
            if (!is_array($doc)) {
                continue;
            }
            $id = $doc['design_resource_document_id'] ?? null;
            if ($id !== null) {
                $unique[$id] = $doc;
            }
        }
        return array_values($unique);
    }

    public function getDesignResourceDocumentsComponentData(array $param)
    {
        $data = $this->getDesignResourceComponentData($param);

        $productIds = array_column($data['items'], 'product_id');
        $productIds = array_unique($productIds);
        $productsCertificates = $this->productCertificate->whereIn('product_id', $productIds)->findAll();
        $certificates = [];
        foreach($productsCertificates as $productCertificate){
            $certificate = [];
            $certificate['name'] = $productCertificate['title'];
            $certificate['format'] = $productCertificate['file_format'];
            $certificate['url'] = '';
            if(is_string($productCertificate['certificate_file'])){
                $file = json_decode($productCertificate['certificate_file'], true);
                if(is_array($file) && count($file) > 0){
                    $certificate['url'] = $file[0]['objectURL'];
                    $certificate['name'] = $file[0]['name'];
                }
            }else if (is_array($productCertificate['certificate_file']) && count($productCertificate['certificate_file']) > 0){
                $certificate['url'] = $productCertificate['certificate_file'][0]['objectURL'];
                $certificate['name'] = $productCertificate['certificate_file'][0]['name'];
            }
            $certificate['product_certificate_id'] = $productCertificate['product_certificate_id'];
            $certificates[$productCertificate['product_id']][] = $certificate;
        }


        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as &$item) {
                if(isset($certificates[$item['product_id']])){
                    $item['design_resource_documents'] = array_merge(($item['design_resource_documents']??[]), $certificates[$item['product_id']]);
                }
                if (
                    isset($item['design_resource_documents'])
                    && is_array($item['design_resource_documents'])
                ) {
                    usort($item['design_resource_documents'], static function ($a, $b): int {
                        $nameA = is_array($a) ? ($a['name'] ?? '') : '';
                        $nameB = is_array($b) ? ($b['name'] ?? '') : '';
                        return strcasecmp((string) $nameA, (string) $nameB);
                    });
                }
            }
            unset($item);

            usort($data['items'], static function ($a, $b): int {
                $titleA = is_array($a) ? ($a['title'] ?? '') : '';
                $titleB = is_array($b) ? ($b['title'] ?? '') : '';
                return strcasecmp((string) $titleA, (string) $titleB);
            });
        }

        return $data;
    }

    public function getDesignResourceFinishesComponentData(array $param)
    {
        $data = $this->getDesignResourceComponentData($param);
        // if (!empty($data['items']) && is_array($data['items'])) {
        //     $brandOrder = [
        //         'laminex' => 0,
        //         'polytec' => 1,
        //         'egger' => 2,
        //         'krost' => 3,
        //     ];
        //     usort($data['items'], static function ($a, $b) use ($brandOrder): int {
        //         $brandA = is_array($a) ? trim((string) ($a['brand'] ?? '')) : '';
        //         $brandB = is_array($b) ? trim((string) ($b['brand'] ?? '')) : '';
        //         $rankA = $brandOrder[strtolower($brandA)] ?? PHP_INT_MAX;
        //         $rankB = $brandOrder[strtolower($brandB)] ?? PHP_INT_MAX;
        //         if ($rankA !== $rankB) {
        //             return $rankA <=> $rankB;
        //         }
        //         $cmpBrand = strcasecmp($brandA, $brandB);
        //         if ($cmpBrand !== 0) {
        //             return $cmpBrand;
        //         }
        //         $titleA = is_array($a) ? ($a['title'] ?? '') : '';
        //         $titleB = is_array($b) ? ($b['title'] ?? '') : '';

        //         return strcasecmp((string) $titleA, (string) $titleB);
        //     });
        // }

        return $data;
    }


    public function getDesignResourceTextilesComponentData(array $param)
    {
        $data = $this->getDesignResourceComponentData($param);
        // if (!empty($data['items']) && is_array($data['items'])) {
        //     usort($data['items'], static function ($a, $b): int {
        //         $typeA = is_array($a) ? ($a['type'] ?? '') : '';
        //         $typeB = is_array($b) ? ($b['type'] ?? '') : '';
        //         $cmp = strcasecmp((string) $typeA, (string) $typeB);
        //         if ($cmp !== 0) {
        //             return $cmp;
        //         }
        //         $titleA = is_array($a) ? ($a['title'] ?? '') : '';
        //         $titleB = is_array($b) ? ($b['title'] ?? '') : '';

        //         return strcasecmp((string) $titleA, (string) $titleB);
        //     });
        // }

        return $data;
    }


    public function getDesignResourceComponentData(array $param): array
    {
        $results = $this->prepareQueryResource($param);
        $results['total_result'] = 'Images: ' . count($results['items']) . ' Results';
        return $results;
    
    }

    public function getDesignResourceComponentData_backup(array $param): array
    {
        $model = 'design_resource';
        $designResourceId = isset($param['design_resource_id']) ? (int) $param['design_resource_id'] : null;
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model
                ->with(['design_resource_documents'])
                ->where('design_resource.resource_type', '=', $param['type'])
                ->select($param['fields']);

            if ($designResourceId) {
                $query->where('design_resource.design_resource_id', '=', $designResourceId);
            }

            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count'] * 1);
            }

            if (
                isset($param['per_page']) &&
                isset($param['current_page']) &&
                $param['current_page'] > 0
            ) {
                $offset = $param['offset'] ?? ($param['current_page'] - 1) * $param['per_page'];
                $limit = ($param['per_page']*$param['current_page'])-$offset;
        
                $query->offset($offset);
                $query->limit($limit);
            }

            $results = $query->findAll();

            foreach ($results as &$designResource) {

                $imageData = json_decode($designResource['img'] ?? '[]', true);
                $imageUrl = '/img/design-resources/vira.png'; // Default image

                if (!empty($imageData) && isset($imageData[0]['objectURL'])) {
                    $imageUrl = $imageData[0]['objectURL'];
                } elseif (!empty($imageData) && isset($imageData[0]['image'])) {
                    $imageUrl = $imageData[0]['image'];
                }
                $designResource['image'] = $imageUrl;
                $designResource['design_resource_documents'] = json_decode($designResource['design_resource_documents'] ?? '[]', true);
            }

            return [
                'total_result' => 'Documents: ' . count($results) . ' Results',
                'items' => $results
            ];
        }
        return [
            'total_result' => 'Documents: 0 Results',
            'items' => []
        ];
    }

    public function getDesignResourceById(int $id, string $resource_type = 'documents'): array
    {

        $designResource = $this->getDesignResourceComponentData([
            'model' => 'design_resource',
            'fields' => [
                'design_resource.design_resource_id',
                'design_resource.title',
                'design_resource.brand',
                'design_resource.type',
                'design_resource.hex_value',
                'design_resource.is_featured',
                'design_resource.grade',
                'design_resource.link_text',
                'design_resource.img',
                'design_resource.img2',
                'design_resource.slug',
                'design_resource.description',
                'design_resource.media_id',
                'design_resource.resource_type'
            ],
            'design_resource_id' => $id,
            'type' => $resource_type
        ]);

        if ($designResource['items'][0]) {
            $baseUrl = env('APP_URL');
            $designResource['items'][0]['img'] = json_decode($designResource['items'][0]['img'] ?? ($designResource['items'][0]->img ?? '[]'), true) ?: [];
            $designResource['items'][0]['img2'] = json_decode($designResource['items'][0]['img2'] ?? ($designResource['items'][0]->img2 ?? '[]'), true) ?: [];
            $updateUrl = $designResource['items'][0]['img'][0]['objectURL'] ?? null;
            $updateUrl2 = $designResource['items'][0]['img2'][0]['objectURL'] ?? null;
            if ($updateUrl) $designResource['items'][0]['img'][0]['objectURL'] = $baseUrl . $updateUrl;
            if ($updateUrl2) $designResource['items'][0]['img2'][0]['objectURL'] = $baseUrl . $updateUrl2;
        }
        return $designResource['items'][0];
    }
    // create design resource
    public function createDesignResource(array $data): array
    {
        // INSERT DESIGN RESOURCE
        $slug = str_replace(' ', '-', strtolower($data['title']));

        // http link cleaner
        $imageCleaner = function ($imageArray) {
            if (!is_array($imageArray) || empty($imageArray[0]['objectURL'])) {
                return [];
            }
            $envUrl = rtrim(env('APP_URL'), '/');
            $url = $imageArray[0]['objectURL'];
            $url = str_replace($envUrl, '', $url);
            $url = preg_replace('#/+#', '/', $url);
            $imageArray[0]['objectURL'] = $url;
    
            return $imageArray;
        };
    
        $img  = $imageCleaner($data['img']  ?? []);
        $img2 = $imageCleaner($data['img2'] ?? []);

        $resourceData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'resource_type' => $data['resource_type'],
            'link_text' => $data['link_text'],
            'img' => json_encode($img),
            'img2' => json_encode($img2),
            'slug' => $slug,
            'brand' => $data['brand'] ?? null,
            'type' => $data['type'] ?? null,
            'is_featured' => $data['is_featured'] ?? 0,
            'grade' => $data['grade'] ?? null,
            'media_id' => $data['img'][0]['media_id'] ?? null,
            'hex_value' => $data['hex_value'] ?? null,
        ];

        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();

            // "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'Amet nulla eum maxi-finishes' for key 'design_resource.uk_design_resource_title_type'"
            // $designResource = $this->model->where('title', '=', $data['title'])->where('type', '=', $data['type'])->first();
            // if ($designResource) {
            //     throw new Exception('Design resource already exists');
            // }


            $designResource = $this->model->create($resourceData);
            if (!$designResource->design_resource_id) {
                throw new Exception('Failed to create design resource');
            }

            $this->db->commit();
            return $this->getDesignResourceById($designResource->design_resource_id, $data['resource_type']);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to create design resource: ' . $e->getMessage());
        }
    }
    public function updateDesignResource(array $data, int $id): array
    {
        try {
            $this->db->beginTransaction();

            $resourceId = $data['design_resource_id'];
            $resourceType = $data['resource_type'];
            $this->model->clearQuery();
            $designResource = $this->model->where('design_resource_id', '=', $resourceId)->where('resource_type', '=', $resourceType)->first();
            if (!$designResource) {
                throw new Exception('Design resource not found');
            }
            $slug = str_replace(' ', '-', strtolower($data['title']));
            $resourceData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'link_text' => $data['link_text'] ?? null,
                'slug' => $slug,
                'brand' => $data['brand'] ?? null,
                'type' => $data['type'] ?? null,
                'is_featured' => $data['is_featured'] ?? 0,
                'grade' => $data['grade'] ?? null,
                'media_id' => $data['media_id'] ?? null,
                'hex_value' => $data['hex_value'] ?? null,
                'resource_type' => $resourceType,
            ];
            $designResource->update($resourceData);
            $this->db->commit();
            return $this->getDesignResourceById($resourceId, $resourceType);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to update design resource document: ' . $e->getMessage());
        }
    }
    // upload design resource
    public function uploadDesignResource($data, string $property, string $resource_type, int $id): array
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            // upload media
            $designResource = $this->model->where('design_resource_id', '=', $id)->where('resource_type', '=', $resource_type)->first();
            if (!$designResource) {
                throw new Exception('Design resource not found for upload');
            }
            $dataobj = $data;
            $img = [];
            foreach ($dataobj as $item) {
                $img[] = [
                    'design_resource_id' => $id,
                    'name' => $item['name'] ?? '',
                    'size' => $item['size'] ?? '',
                    'type' => $item['type'] ?? '',
                    'image' => $item['image'] ?? '',
                    'file' => [
                        "name" => $item['name'] ?? '',
                        "size" => $item['size'] ?? '',
                        "type" => $item['type'] ?? '',
                        "error" => $item['error'] ?? 0,
                        "tmp_name" => $item['tmp_name'] ?? '',
                        "full_path" => $item['full_path'] ?? '',
                    ],
                    'status' => isset($item['status']) && is_array($item['status'])
                        ? $item['status']
                        : ['name' => 'Uploaded', 'severity' => 'success'],
                    'media_id' => $item['media_id'] ?? null,
                    'objectURL' => ($item['objectURL'] ?? ''),
                    'created_at' => date('Y-m-d H:i:s'),
                    'description' => $item['description'] ?? '',
                    'design_resource_image_id' => $id,
                ];
            }
            $imgJson = json_encode($img);
            $mediaId =  $img[0]['media_id'];
            $designResource->update([$property => $imgJson], ['media_id' => $mediaId]);
            $this->db->commit();
            // return $this->getDesignResourceById($id, $designResource->resource_type);
            return $img;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to upload design resource: ' . $e->getMessage());
        }
    }

    // upload design resource document
    public function uploadDesignResourceDocument(array $data, string $resource_type, int $id): array
    {
        try {
            $this->db->beginTransaction();
            // check if design resource document already exists
            $document = $this->model->where('design_resource_id', '=', $id)->where('resource_type', '=', $resource_type)->first();
            if (!$document) {
                throw new Exception('Design resource document not found');
            }
            // prepare document data
            $documents = [];
            foreach ($data as $item) {
                $documents[] = [
                    'design_resource_id' => $id,
                    'name' => $item['name'] ?? '',
                    'format' => $item['format'] ?? $data[0]['type'] ?? '',
                    'description' => $item['description'] ?? '',
                    'media_id' => $item['media_id'] ?? null,
                    'url' => ($item['path'] ?? '')
                ];
            }
            // upload document
            $this->designResourceDocument->clearQuery();
            $this->designResourceDocument->upsert($documents, ['design_resource_id', 'media_id']);
            $this->db->commit();
            // return $this->getDesignResourceById($id, $resource_type);
            return $documents;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to upload design resource document: ' . $e->getMessage());
        }
    }
    // get design resource document types
    public function getDesignResourceDocumentTypes(string $resource_type = 'documents'): array
    {
        return $this->model->where('resource_type', '=', $resource_type)->select(['type as id', 'type as name'])->groupBy('type')->findAll(false);

    // SELECT TYPE AS id,
    //     TYPE AS NAME
    // FROM
    //     `design_resource`
    // WHERE
    //     resource_type = 'finishes'
    // GROUP BY TYPE
    }
    public function deleteDesignResourceDocument(int $id): bool
    {
        return $this->deleteDesignResource($id);
    }
    public function deleteDesignResourceModel(int $id): bool
    {
        return $this->deleteDesignResource($id);
    }
    public function deleteDesignResourceFinish(int $id): bool
    {
        return $this->deleteDesignResource($id);
    }
    public function deleteDesignResourceTextile(int $id): bool
    {
        return $this->deleteDesignResource($id);
    }

    public function deleteDesignResource(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->delete($id);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception('Failed to delete design resource: ' . $e->getMessage());
        }
    }

    /**
     * @param  list<int>  $ids  design_resource_document_id values
     * @return array{success: bool, deleted_ids: list<int>, property: string}
     */
    public function deleteDesignResourceModelById(array $ids, string $property = 'models'): array
    {
        $deletedIds = [];
        $this->designResourceDocument->clearQuery();
        $deleted = $this->designResourceDocument->deleteMultiple($ids);

        if($deleted > 0){
            return [
                'success' => true,
                'deleted_ids' => $ids,
                'property' => $property,
            ];
        }
        return [
            'success' => false,
            'deleted_ids' => [],
            'property' => $property,
        ];
    }

    public function deleteDesignResourceDocRecord(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $designResourceDocument = $this->designResourceDocument->where('design_resource_document_id', '=', $id)->first();
            if (!$designResourceDocument) {
                throw new Exception('Design resource document not found');
            }
            $mediaId = $designResourceDocument->media_id;
            $this->designResourceDocument->delete($id);
            if($mediaId){
                $media = $this->media->where('media_id', '=', $mediaId)->first();
                if($media){
                    $filePath = $media->data?->file ? json_decode($media->data?->file, true)['path'] ?? null : null;
                    $this->media->delete($mediaId);
                }
            }
            if(!isset($filePath) || !$filePath){
                $filePath = $designResourceDocument->url;
            }
            $this->db->commit();
            if(isset($filePath) && $filePath){
                $filePath = ROOT_DIR . DS . 'public' . $filePath;
                if (file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
            }
            return true;
        } catch (Exception $e) {   
            $this->db->rollBack();
            throw new Exception('Failed to delete design resource document record: ' . $e->getMessage());
        }
    }

    // ============================= account resource filter api =============================
    public function filterCategoriesByContextType(string $context_type, string $resource_type = "")
    {
        $this->model->clearQuery();
        switch($context_type){
            case 'product':
            case 'post':
                $query =  $query = $this->taxonomy
                ->where('taxonomy.post_type', '=', $context_type)
                ->where('taxonomy.type', '=', 'categories')
                ->join('taxonomy_item', 'taxonomy_item.taxonomy_id', '=', 'taxonomy.taxonomy_id')
                ->whereNotNull('taxonomy_item.parent_id')
                ->select(['taxonomy_item.taxonomy_item_id as id', 'taxonomy_item.name as name'])
                ->groupBy('taxonomy_item.name');
                break;
            case 'project':
                return [];
                break;
            case 'showrooms':
                $query = $this->showroom
                ->select(['showrooms_id as id', 'title as name'])
                ->where('is_section_active','=','1');
                // ->groupBy('project_sections_id')
                // ->groupBy('project_sections.title');
                break;
            case 'brand':
                $query = $this->model
                ->whereNotNull('brand')
                ->select(['brand as id', 'brand as name'])
                ->groupBy('brand');
                if($resource_type){
                    $query->where('resource_type', '=', $resource_type);
                }
                break;
            case 'type':
                $query = $this->model
                ->whereNotNull('type')
                ->select(['type as id', 'type as name'])
                ->groupBy('type');
                if($resource_type){
                    $query->where('resource_type', '=', $resource_type);
                }
                break;
        }
        // if($context_type !== 'showrooms'){

        //     $query = $this->taxonomy
        //     ->where('taxonomy.post_type', '=', $context_type)
        //     ->where('taxonomy.type', '=', 'categories')
        //     ->join('taxonomy_item', 'taxonomy_item.taxonomy_id', '=', 'taxonomy.taxonomy_id')
        //     ->whereNotNull('taxonomy_item.parent_id')
        //     ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item.name'])
        //     ->groupBy('taxonomy_item.name')
        //     ->findAll(false);

        //     $result = [];
        //     $result = array_map(function($row){
        //         return [
        //             'id' => $row['taxonomy_item_id'],
        //             'name' => $row['name']
        //         ];
        //     }, $query);

        //     array_unshift($result, ['id' => '', 'name' => 'Select A Category']);
            
        //     return $result;
          
        // }else{
        //     $query = $this->showroom
        //     ->select(['showrooms_id', 'title'])
        //     ->findAll(false);

        //     $result = [];
        //     $result = array_map(function($row){
        //         return [
        //             'id' => $row['showrooms_id'],
        //             'name' => $row['title']
        //         ];
        //     }, $query);

        //     $result = [
        //         'result' => $result,
        //         'showrooms' => 'showrooms',
        //     ];
        //     return $result; 
        // }

        $results = $query->findAll();

        // $result = [];
        // $result = array_map(function($row){
        //     return [
        //         'id' => $row['taxonomy_item_id'],
        //         'name' => $row['name']
        //     ];
        // }, $data);

        // array_unshift($results, ['id' => '', 'name' => 'Select An Option']);
        
        return $results;

    }

    public function filterCategroyIdByCategoryName(int $category_id)
    {

        $query = $this->productToTaxonomyItem
        ->join('product_content', 'product_content.product_id', '=', 'product_to_taxonomy_item.product_id')
        ->where('taxonomy_item_id', '=', $category_id)
        ->select(['product_content.name', 'product_content.product_id'])
        ->findAll(false);
        
       
        $result = [];
        $result = array_map(function($row){
            return [
                'id' => $row['product_id'],
                'name' => $row['name']
            ];
        }, $query);
        
        return $result;
    }

    public function filterModelNameByModelId(string $model_type, int $model_id, string $model_name): array
    {
        switch($model_type){
            case 'product':
                $query = $this->product
                ->where('product.product_code', 'LIKE', '%' . $model_name . '%')
                ->select(['product.product_id as id', 'product.product_code as name'])
                ->orderBy('product.product_id', 'DESC')
                ->groupBy('product.product_id');
                if($model_id && $model_id > 0){
                    $query->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id');
                    $query->where('product_to_taxonomy_item.taxonomy_item_id', '=', $model_id);
                }
                break;
            case 'brand':
                $query = $this->model
                ->distinct()
                ->where('brand', 'LIKE', '%' . $model_name . '%')
                ->whereIn('resource_type', ['finishes', 'textiles'])
                ->select(['brand as id', 'brand as name'])
                ->groupBy('brand')
                ->groupBy('design_resource_id');
                break;
            case 'project':
                $query = $this->project
                ->where('project.name', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project.title', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project.preview_text', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project.main_title', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project.slug', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project.description', 'LIKE', '%' . $model_name . '%')
                ->select(['project.project_id as id', 'project.name as name'])
                ->orderBy('project.project_id', 'DESC');
                break;
            case 'post':
                $query = $this->post
                ->join('post_to_taxonomy_item', 'post_to_taxonomy_item.post_id', '=', 'post.post_id')
                ->where('post_id', '=', $model_id)
                ->where('post.title', 'LIKE', '%' . $model_name . '%')
                ->select(['post.post_id as id', 'post.title as name'])
                ->orderBy('post.post_id', 'DESC');
                break;
            case 'showrooms':
                $query = $this->projectSection
                ->where('project_sections.title', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project_sections.slug', 'LIKE', '%' . $model_name . '%')
                ->orWhere('project_sections.description', 'LIKE', '%' . $model_name . '%')
                ->select(['project_sections.project_sections_id as id', 'project_sections.title as name'])
                ->orderBy('project_sections.project_sections_id', 'DESC');
                break;
        }
        $query->limit(50);
        $results = $query->findAll(false);
        return $results;
    }
     // ============================= get finishes data by typ api =============================
    public function getTextilesDataByType(string $type)
    {
        $query = $this->model->where('resource_type', '=', 'textiles')
        ->where('type', '=', $type)->findAll(false);
        $result = [];
        $result = array_map(function($row){
            $imgArr = json_decode($row['img'] ?? '', true);
            $img2Arr = json_decode($row['img2'] ?? '', true);

            // Default to empty string
            $imgUrl = '';

            if (is_array($imgArr) && isset($imgArr[0]['objectURL']) && !empty($imgArr[0]['objectURL'])) {
                $imgUrl = $imgArr[0]['objectURL'];
            } elseif (is_array($img2Arr) && isset($img2Arr[0]['objectURL']) && !empty($img2Arr[0]['objectURL'])) {
                $imgUrl = $img2Arr[0]['objectURL'];
            }

            return [
                'id' => $row['design_resource_id'],
                'name' => $row['title'],
                'image' => $imgUrl,
                // 'link_text' => $row['link_text'],
                // 'grade' => $row['grade'],
                // 'resource_type' => $row['resource_type'],
                // 'is_featured' => $row['is_featured'],
                // 'media_id' => $row['media_id'],
                // 'hex_value' => $row['hex_value'],
                // 'slug' => $row['slug'],
                // 'description' => $row['description'],
            ];
        }, $query);
        return $result;
    }

    // download model data
    public function getModelData(int $product_id, array $resource_types = [])
    {
        $query = $this->designResourceDocument
        ->join('design_resource', 'design_resource_document.design_resource_id', '=', 'design_resource.design_resource_id')
        ->join('product_resource', 'product_resource.design_resource_id', '=', 'design_resource.design_resource_id');
        if(isset($product_id) && $product_id > 0){
            $query->where('product_resource.product_id', '=', $product_id);
        }

        if(isset($resource_type) && count($resource_type) > 0){
            $query->whereIn('design_resource.resource_type', $resource_type);
        }
        $query->select(['design_resource_document.*']);
        $results = $query->findAll(false);
        //make a link for each design_resource_document download_url
        return $results;
    }

    public function globalSearch(string $queryString)
    {
        $popularSearch = $this->insertPopularSearch($queryString);
        $results = $this->prepareQueryGlobalSearch($queryString);
        // make a global search data object
        $allItems = array_map(function($item){
            return new GlobalSearchData($item);
        }, $results);
        // $allItems['popular_search'] = $popularSearch;
   
        $total_count = count($allItems);
        return [
            'total_result' => 'Results: ' . $total_count . ' Results',
            'results' => $allItems,
            // 'popular_search' => $popularSearch,
        ];
    }

    // ============================= popular search api =============================
    private function insertPopularSearch(string $queryString)
    {
        // check if the query string is already in the popular search table
        $this->popularSearch->clearQuery();
        $popularSearch = $this->popularSearch->where('search_key', '=', $queryString)->first();
        if($popularSearch){
            $this->popularSearch
            ->where('popular_search_id', '=', $popularSearch->popular_search_id)
            ->update([
                'search_count' => $popularSearch->search_count + 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }else{
            $data = [
                'search_key' => $queryString,
                'search_count' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->popularSearch->create($data);
        }

        // list the popular search data
        $this->popularSearch->clearQuery();
        $popularSearchList = $this->popularSearch
            ->orderBy('search_count', 'DESC')
            ->limit(10)
            ->findAll();
        return $popularSearchList ?? [];
    }

    public function getPopularSearch()
    {
        // list the popular search data
        $this->popularSearch->clearQuery();
        $popularSearchList = $this->popularSearch
            ->orderBy('search_count', 'DESC')
            ->limit(6)
            ->findAll();
        return $popularSearchList ?? [];
    }

    public function globalSearchByContext(
        string $queryString,
        string $contexts = '',
        int $perPage = 40,
        int $currentPage = 1,
        int $offset = 0,
    ): array {
        $perPage = max(1, $perPage);
        $currentPage = max(1, $currentPage);
        $offset = max(0, $offset);

        $prepared = $this->prepareQueryGlobalSearchByContext($queryString, $contexts, [
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'offset' => $offset,
        ]);

        /** @var list<array<string, mixed>> $resultRows */
        $resultRows = $prepared['items'];
        $total = $prepared['total'];
        $appliedOffset = $prepared['applied_offset'];

        $pageItems = array_map(function ($item) {
            return new GlobalSearchData($item);
        }, $resultRows);

        $sliceCount = count($pageItems);
        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 1;
        $loadedData = min(max(0, $appliedOffset + $sliceCount), $total);
        $hasMore = $loadedData < $total;

        return [
            'total_result' => 'Results: ' . $total . ' Results',
            'results' => $pageItems,
            'pagination' => [
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'offset' => $appliedOffset,
                'total' => $total,
                'last_page' => $lastPage,
                'count' => $sliceCount,
                'loaded_data' => $loadedData,
                'has_more' => $hasMore,
            ],
        ];
    }

    private function prepareQueryGlobalSearch(string $queryString): array
    {
        $productQuery = $this->product;
        $mediaQuery = $this->media;
        $projectQuery = $this->project;
        $projectSectionQuery = $this->projectSection;
        $postQuery = $this->post;
        // $designResourceQuery = $this->model;

        $products = $productQuery
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id')
        ->join('taxonomy_item', 'taxonomy_item.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id')
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->select([
            'product_id as id',
            'product_content.title as title', 
            'product_content.name as name', 
            'image_thumb as image', 
            'product_content.tag_line as description', 
            'product_content.slug as slug',
            'CONCAT("products reference: ", product.product_id) as reference',
            'CONCAT("products/", taxonomy_item_content.slug, "/", product_content.slug) as href',
            'CONCAT("Product-", product.product_id) as model_type',
         ])
        ->where('product.status', '=', 1)

        // ->where('product.product_code', 'LIKE', '%' . $queryString . '%')
        // // ->orWhere('product_content.slug', 'LIKE', '%' . $queryString . '%')
        // // ->orWhere('product_content.name', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('product_content.title', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('product_content.tag_line', 'LIKE', '%' . $queryString . '%')

        ->where(function($q) use ($queryString) {
            return $q->where('product.product_code', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product_content.title', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product.feature_description', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product.feature_image_one_description', 'LIKE', '%' . $queryString . '%')
            ->orWhere('taxonomy_item_content.name', 'LIKE', '%' . $queryString . '%')
            ->orWhere('taxonomy_item_content.slug', 'LIKE', '%' . $queryString . '%');
        })
        // ->groupBy('product.product_id')
        ->limit(50)
        ->findAll();

        $projects = $projectQuery
        ->select([
            'project_id as id', 
            'name as name', 
            'image_thumb as image',
            'keyline_quote as description',
            // 'preview_text as description',
            'slug',
            'CONCAT("projects reference: ", project.project_id) as reference',
            'CONCAT("projects/", project.slug) as href',
            'CONCAT("Project-", project.project_id) as model_type',
        ])
        ->where('name', 'LIKE', '%' . $queryString . '%')
        ->orWhere('slug', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('keyline_quote', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('preview_text', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('main_title', 'LIKE', '%' . $queryString . '%')
        ->orderBy('created_at', 'DESC')
        ->orderBy('project_id', 'DESC')
        ->limit(15)
        ->findAll(false);

        $posts = $postQuery
        ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
        ->select([
            'post_id as id', 
            'title as name', 
            'feature_image_thumb as image', 
            'keyline_quote as description', 
            // 'description', 
            'post_content.slug as slug',
            'CONCAT("blog reference: ", post.post_id) as reference',
            'CONCAT("blog/", post_content.slug) as href',
            'CONCAT("Blog-", post.post_id) as model_type',
        ])
        ->where('post.type', '=', 'post')
        ->where('post.title', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('post.keyline_quote', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('post_content.slug', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('post_content.name', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('post_content.excerpt', 'LIKE', '%' . $queryString . '%')
        ->orWhere('post_content.meta_title', 'LIKE', '%' . $queryString . '%')
        ->limit(15)
        ->orderBy('created_at', 'DESC')
        ->orderBy('post_id', 'DESC')
        ->findAll(false);

        $showrooms = $projectSectionQuery
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'project_sections.showroom_id')
        ->select([
            'project_sections_id as id', 
            'title as name', 
            'image', 
            'description', 
            'project_sections.slug as slug',
            'CONCAT("showroom section reference: ", project_sections.project_sections_id) as reference',
            'CONCAT("showroom/", showrooms.slug) as href',
            'CONCAT("Showroom-", project_sections.project_sections_id) as model_type',
        ])
        ->where('title', 'LIKE', '%' . $queryString . '%')
        ->orWhere('slug', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('description', 'LIKE', '%' . $queryString . '%')
        ->limit(15)
        ->findAll(false);

        // $designResources = $designResourceQuery
        // ->select(['design_resource_id as id', 'title as name', 
        // 'img as image', 
        // 'description',
        // 'CONCAT("Design Resource-", design_resource.design_resource_id) as model_type',
        // ])
        // ->where('title', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('description', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('brand', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('type', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('grade', 'LIKE', '%' . $queryString . '%')
        // ->orWhere('slug', 'LIKE', '%' . $queryString . '%')
        // ->limit(15)
        // ->findAll(false);
        $mergedResults = array_merge($products, $projects, $showrooms, $posts);

        return $mergedResults;
    }

    /**
     * Build merged global search rows (all contexts), then slice with the same offset/limit rules as
     * prepareQueryImages: optional item_count cap, then per_page / current_page / offset paging.
     *
     * @param  array<string, int|string|float|null> $params
     * @return array{
     *     items: list<array<string, mixed>>,
     *     total: int,
     *     applied_offset: int,
     *     applied_limit: int
     * }
     */
    private function prepareQueryGlobalSearchByContext(string $queryString, string $contexts = '', array $params = []): array
    {
        // If no context passed → allow all
        if (empty($contexts)) {
            $contextsList = ['product', 'project', 'post', 'showrooms'];
        } else {
            $contextsList = array_map('trim', explode(',', $contexts));
        }

        $results = [];

        // PRODUCTS
        if (in_array('product', $contextsList, true)) {
            $products = $this->product
                ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
                ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id')
                ->join('taxonomy_item', 'taxonomy_item.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id')
                ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
                ->select([
                    'product_id as id',
                    'product_content.title as title',
                    'product_content.name as name',
                    'image_thumb as image',
                    'product_content.tag_line as description',
                    'product_content.slug as slug',
                    'CONCAT("products reference: ", product.product_id) as reference',
                    'CONCAT("products/", taxonomy_item_content.slug, "/", product_content.slug) as href',
                    'CONCAT("Product-", product.product_id) as model_type',
                ])
                ->where('product.status', '=', 1);
            if (isset($queryString) && $queryString !== '' && $queryString !== 'null') {
                $products->where(function ($q) use ($queryString) {
                    return $q->where('product.product_code', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('product_content.title', 'LIKE', '%' . $queryString . '%')
                        // ->orWhere('product_content.tag_line', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('product.feature_description', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('product.feature_image_one_description', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('taxonomy_item_content.name', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('taxonomy_item_content.slug', 'LIKE', '%' . $queryString . '%');
                });
            }
            $results = array_merge($results, $products->findAll());
        }

        // PROJECTS
        if (in_array('project', $contextsList, true)) {
            $projects = $this->project
                ->select([
                    'project_id as id',
                    'name as name',
                    'image_thumb as image',
                    'keyline_quote as description',
                    'slug',
                    'CONCAT("projects reference: ", project.project_id) as reference',
                    'CONCAT("projects/", project.slug) as href',
                    'CONCAT("Project-", project.project_id) as model_type',
                ]);
            if (isset($queryString) && $queryString !== '' && $queryString !== 'null') {
                $projects->where(function ($q) use ($queryString) {
                    return $q->where('name', 'LIKE', '%' . $queryString . '%')
                        ->orWhere('slug', 'LIKE', '%' . $queryString . '%');
                });
            }
            $results = array_merge($results, $projects->orderBy('created_at', 'DESC')->findAll(false));
        }

        // POSTS (BLOG)
        if (in_array('post', $contextsList, true)) {
            $posts = $this->post
                ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
                ->select([
                    'post_id as id',
                    'title as name',
                    'feature_image_thumb as image',
                    'keyline_quote as description',
                    'post_content.slug as slug',
                    'CONCAT("blog reference: ", post.post_id) as reference',
                    'CONCAT("blog/", post_content.slug) as href',
                    'CONCAT("Blog-", post.post_id) as model_type',
                ]);
            $posts->where('post.type', '=', 'post');
            if (isset($queryString) && $queryString !== '' && $queryString !== 'null') {
                $posts->where('post.title', 'LIKE', '%' . $queryString . '%')
                    ->orWhere('post_content.meta_title', 'LIKE', '%' . $queryString . '%');
            }
            $results = array_merge($results, $posts->orderBy('created_at', 'DESC')->findAll(false));
        }

        // SHOWROOMS SECTIONS
        if (in_array('showrooms', $contextsList, true)) {
            $showrooms = $this->projectSection
                ->join('showrooms', 'showrooms.showrooms_id', '=', 'project_sections.showroom_id')
                ->select([
                    'project_sections_id as id',
                    'title as name',
                    'image',
                    'description',
                    'project_sections.slug as slug',
                    'CONCAT("showroom section reference: ", project_sections.project_sections_id) as reference',
                    'CONCAT("showroom/", showrooms.slug) as href',
                    'CONCAT("Showroom-", project_sections.project_sections_id) as model_type',
                ]);
            if (isset($queryString) && $queryString !== '' && $queryString !== 'null') {
                $showrooms->where('title', 'LIKE', '%' . $queryString . '%')
                    ->orWhere('slug', 'LIKE', '%' . $queryString . '%');
            }
            $results = array_merge($results, $showrooms->findAll(false));
        }

        $totalCount = count($results);
        [$data, $offsetUsed, $limitUsed] = $this->prepareQueryImagesStylePagination($results, $params);

        return [
            'items' => $data,
            'total' => $totalCount,
            'applied_offset' => $offsetUsed,
            'applied_limit' => $limitUsed,
        ];
    }

    /**
     * Offset/limit for a numeric list mirroring prepareQueryImages: item_count trim, then
     * $offset = $params['offset'] ?? (($current_page - 1) * $per_page) and $limit = $per_page*$current_page - $offset.
     *
     * @param  array<int|string, mixed>              $rows
     * @param  array<string, int|string|null|float>   $params
     * @return array{0: list<array<string, mixed>>, 1: int, 2: int}
     */
    private function prepareQueryImagesStylePagination(array $rows, array $params): array
    {
        /** @var list<array<string, mixed>> $rowList */
        $rowList = array_values($rows);

        if (isset($params['item_count']) && (int) $params['item_count'] > 0) {
            $rowList = array_slice($rowList, 0, (int) $params['item_count']);
        }

        $data = $rowList;
        $offsetUsed = 0;
        $limitUsed = count($rowList);

        if (
            isset($params['per_page'])
            && isset($params['current_page'])
            && (int) $params['current_page'] > 0
        ) {
            $perPage = (int) $params['per_page'];
            $currentPage = (int) $params['current_page'];
            $offsetUsed = array_key_exists('offset', $params)
                ? (int) $params['offset']
                : (($currentPage - 1) * $perPage);
            $limitUsed = ($perPage * $currentPage) - $offsetUsed;

            $data = $limitUsed >= 1 ? array_slice($rowList, $offsetUsed, $limitUsed) : [];
        }

        return [$data, $offsetUsed, $limitUsed];
    }
    // ============================= get resources by desk api =============================
    public function getResourcesByDesk(string $search_query, string $resource_type)
    {
        if($resource_type == 'finishes' || $resource_type == 'documents'){
            $query = $this->model
            ->select(['design_resource_id as id', 'title as name', 'description'])
            ->where('resource_type', '=', $resource_type)
            ->where('title', 'LIKE', '%' . $search_query . '%')
            ->limit(50)
            ->findAll(false);
        }else{
            $query = $this->productVariant
            ->select(['product_variant_id as id', 'product_id', 'variant_name as name', 'variant_description as description'])
            ->where('variant_name', 'LIKE', '%' . $search_query . '%')
            ->limit(50)
            ->findAll(false);
        }
        return $query;
    }

    public function getDesignResourceByIdByResourceType(int $id, string $resource_type = 'documents'): array
    {
        $this->model->clearQuery();
        $query = $this->model
        ->where('design_resource.resource_type', '=', $resource_type);
        // ->select($param['fields']);
        $query->with(['design_resource_documents']);
        $query->select(['design_resource.*']);
        if ($id) {
            $query->where('design_resource.design_resource_id', '=', $id);
        }
        $result = $query->first();
        // if (isset($result->data) && $result->data) {
        //     return [];
        // }

        $designResource = (array) ($result->data ?? []);
        if ($designResource) {
            $baseUrl = env('APP_URL');
            $designResource['image_thumb_url'] = $this->imageFormat($designResource['image_thumb_url'] ?? '');
            $designResource['img'] = json_decode($designResource['img'] ?? '[]', true);
            $designResource['img2'] = json_decode($designResource['img2'] ?? '[]', true);
            $updateUrl = $designResource['img']['objectURL'] ?? null;
            $updateUrl2 = $designResource['img2']['objectURL'] ?? null;
            if ($updateUrl) $designResource['img']['objectURL'] = $baseUrl . $updateUrl;
            if ($updateUrl2) $designResource['img2']['objectURL'] = $baseUrl . $updateUrl2;

            // data_type :  "file"
            // size :  ""
            // extension :  "DS_Store"

            $designResource['design_resource_documents'] = json_decode($designResource['design_resource_documents'] ?? '[]', true);
        }
        return $designResource ? $designResource : [];
    }


    private function imageFormat(string $image): array
    {
       // name is last part of the image path 
       $imagePath = explode('/', $image);
       $name = end($imagePath);
       
       return [
            'name' => $name,
            'size' => 0,
            'type' => 'image/jpeg',
            'image' => $image,
            'status' => ['name' => 'Uploaded', 'severity' => 'success'],
            'media_id' => null,
            // 'objectURL' => $imageServer . ($item['objectURL'] ?? ''),
            'objectURL' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'product_id' => null,
            'post_image_id' => null,
            'product_image_id' => null,
            'project_section_images_id' => null,
       ];
    }

    public function relatedResourceSearch(string $search): array
    {
        $this->model->clearQuery();
        $result = $this->model
            // ->whereIn('design_resource.resource_type', ['models'])
            ->where('design_resource.title', 'LIKE', '%' . $search . '%')
            ->orWhere('design_resource.slug', 'LIKE', '%' . $search . '%')
            ->select(['design_resource.design_resource_id', 'design_resource.title as name', 'design_resource.slug', 'design_resource.resource_type as type'])
            ->limit(50)
            ->findAll(false);

        // $baseUrl = env('APP_URL');
        // foreach ($result as &$project) {
        //     // $images = json_decode($project['image'], true);
        //     $images =isset($project['image']) && !empty($project['image']) ? json_decode($project['image'], true) : [];
        //     $project['image'] = isset($images[0]['objectURL']) && !empty($images[0]['objectURL']) ? $images[0]['objectURL'] : '';
        //     // not more 20 characters in the description
        //     // $tagLine = isset($project['tag_line']) ? (string) $project['tag_line'] : '';
        //     // $project['description'] = strlen($tagLine) > 20 ? substr($tagLine, 0, 20) . '...' : $tagLine;
        // }

        return $result;
    }
    public function updateModelDocumentFormat(array $document): array | bool
    {
        $doc = $this->designResourceDocument->where('design_resource_document_id', '=', $document['design_resource_document_id'])->first();
        if(!$document){
            return false;
        }
        $doc->update([
            'format' => $document['format'],
        ]);
        return $document;
    }

}
