<?php

declare(strict_types=1);

namespace App\Core\Repositories\Design;

use App\Core\Models\Design\DesignResource;

interface DesignResourceRepositoryInterface
{

    /**
     * Get design resource data for main component
     */
    // public function getOurDesignResourceComponentData(array $param);

    /**
     * Insert multiple design resources
     */
    public function insertDesignResources(array $data): bool;

    /**
     * Get design resource main data for main component
     */
    public function getDesignResourceMainComponentData(array $param);

    /**
     * Get featured materials data for featured materials slider component
     */
    public function getFeaturedMaterialSliderComponentData(array $param);

    public function getFeaturedMaterialSlider(array $param);

    /**
     * Import finishes from CSV file
     */
    public function importResources(string $csv_file, string $resource_type): array;
    public function importDesignResources(string $csv_file, string $resource_type = 'documents'): array;





    // ================================= tab content data =======================
    /**
     * Get design resource images data for images component
     */
    public function getDesignResourceImagesComponentData(array $param);

    /**
     * Get design resource models data for models component
     */
    public function getDesignResourceModelsComponentData(array $param);

     /**
     * Get design resource documents data for documents component
     */
    public function getDesignResourceDocumentsComponentData(array $param);

    /**
     * Get design resource finishes data for finishes component
     */
    public function getDesignResourceFinishesComponentData(array $param);

    /**
     * Get design resource textiles data for textiles component
     */
    public function getDesignResourceTextilesComponentData(array $param);




    // public function getDesignResourceFinishesTextilesComponentData(array $param);
    // public function getDesignResource(string $resource_type = 'documents'): array;
    public function getDesignResourceById(int $id, string $resource_type = 'documents'): array;
    public function createDesignResource(array $data): array;

    public function getDesignResourceDocumentTypes(string $resource_type = 'documents'): array;
    // upload design resource
    public function uploadDesignResource(array $data, string $property, string $resource_type, int $id): array;
    // upload design resource document
    public function uploadDesignResourceDocument(array $data, string $resource_type, int $id): array;

    public function deleteDesignResourceDocument(int $id): bool;
    public function deleteDesignResourceModel(int $id): bool;
    public function deleteDesignResourceFinish(int $id): bool;
    public function deleteDesignResourceTextile(int $id): bool;
    
    public function deleteDesignResourceDocRecord(int $id): bool;

    /**
     * @param  list<int>  $ids  design_resource_document_id values
     * @return array{success: bool, deleted_ids: list<int>, skipped_ids: list<int>, property: string}
     */
    public function deleteDesignResourceModelById(array $ids, string $property = 'models'): array;


    // account resource filter api 
    public function filterCategoriesByContextType(string $context_type);
    // public function filterCategroyIdByCategoryName(int $category_id);
    public function filterModelNameByModelId(string $model_type, int $model_id, string $model_name);

    // get finishes data by grade
    public function getTextilesDataByType(string $grade);

    // download model data
    public function getModelData(int $product_id, array $resource_types = []);

    // global search
    public function globalSearch(string $queryString);

    // global search by context
    /**
     * @return array<string, mixed>
     */
    public function globalSearchByContext(
        string $queryString,
        string $contexts = '',
        int $perPage = 40,
        int $currentPage = 1,
        int $offset = 0,
    ): array;

    // get resources by desk
    public function getResourcesByDesk(string $search_query, string $resource_type);

    // get popular search
    public function getPopularSearch();
    public function getDesignResourceByIdByResourceType(int $id, string $resource_type = 'documents'): array;
    public function relatedResourceSearch(string $search): array;

    public function updateModelDocumentFormat(array $document): array | bool;
} 