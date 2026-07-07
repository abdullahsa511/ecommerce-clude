<?php

declare(strict_types=1);

namespace App\Core\Repositories\Showroom;

use App\Core\Http\Response;
use PDO;
use App\Core\Models\Showroom\Showroom;
use App\Core\Models\Showroom\ShowroomContact;
use App\Core\Models\Showroom\ContactTimeSlot;
use App\Core\Models\Showroom\ProjectSection;
use App\Core\Models\Showroom\ProjectSectionProduct;
use App\Core\Models\Showroom\ProjectSectionImage;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Base\Model;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductOption;
use App\Core\Repositories\Media\MediaRepository;
use App\Core\Repositories\Product\ProductRepository;
use App\Core\Utilities\Debug;
use Exception;
use App\Core\Validation\ShowroomDataValidation;
use App\Core\Validation\ShowroomContactDataValidation;
use App\Core\Validation\ShowroomSectionImageValidation;
use App\Core\Validation\ShowroomSectionProductValidation;
use League\Csv\Reader;
use App\Core\Models\Media\Media;
use function App\Core\System\utils\makeSlug;

use function App\Core\System\utils\env;

class ShowroomRepository extends BaseRepository implements ShowroomRepositoryInterface
{
    private Product $Product;
    private ProjectSection $ProjectSection;
    private ProjectSectionProduct $projectSectionProduct;
    private ProjectSectionImage $projectSectionImage;
    private TaxonomyItem $taxonomyItem;
    private ProductOption $productOption;
    private MediaRepository $mediaRepository;
    private ShowroomContact $showroomContact;
    private ContactTimeSlot $contactTimeSlot;
    private Media $media;
    private ProductRepository $ProductRepository;
    public function __construct(
        PDO $db,
        Product $Product,
        TaxonomyItem $taxonomyItem,
        ProductOption $productOption,
        ProjectSection $ProjectSection,
        ProjectSectionProduct $projectSectionProduct,
        ProjectSectionImage $projectSectionImage,
        MediaRepository $mediaRepository,
        ShowroomContact $showroomContact,
        ContactTimeSlot $contactTimeSlot,
        Media $media,
        ProductRepository $ProductRepository
    ) {
        parent::__construct($db, 'showrooms', Showroom::class);
        $this->ProjectSection = $ProjectSection;
        $this->ProjectSection->setDB($db);
        $this->projectSectionProduct = $projectSectionProduct;
        $this->projectSectionProduct->setDB($db);
        $this->projectSectionImage = $projectSectionImage;
        $this->projectSectionImage->setDB($db);
        $this->taxonomyItem = $taxonomyItem;
        $this->taxonomyItem->setDB($db);
        $this->productOption = $productOption;
        $this->productOption->setDB($db);
        $this->Product = $Product;
        $this->Product->setDB($db);
        $this->mediaRepository = $mediaRepository;
        $this->showroomContact = $showroomContact;
        $this->showroomContact->setDB($db);
        $this->contactTimeSlot = $contactTimeSlot;
        $this->contactTimeSlot->setDB($db);
        $this->media = $media;
        $this->media->setDB($db);
        $this->ProductRepository = $ProductRepository;
    }

    public function getShowroom($options = [])
    {
        $query = $this->model;

        // Filter by ID if provided
        if (!empty($options['id'])) {
            $query = $query->where('showrooms_id', '=', $options['id']);
        }

        // Optional status filter
        if (!empty($options['status'])) {
            $query = $query->where('status', $options['status']);
        }

        // Sorting
        $sortBy = $options['sortBy'] ?? 'sort_order';
        $sortOrder = $options['sortOrder'] ?? 'ASC';
        $query = $query->orderBy($sortBy, $sortOrder);

        // Pagination
        if (!empty($options['limit'])) {
            $offset = isset($options['offset']) ? (int) $options['offset'] : 0;
            $query = $query->limit((int) $options['limit'], $offset);
        }

        // Execute query
        $result = isset($options['id']) ? $query->first() : $query->findAll();

        $data = [];

        if ($result) {
            if (isset($options['id'])) {
                // Single object
                $itemArr = (array) $result;
                $itemArr['image'] = isset($itemArr['image']) ? json_decode($itemArr['image'], true) : [];
                $data = $itemArr;
            } else {
                // Multiple objects
                foreach ($result as $item) {
                    $itemArr = (array) $item;
                    $itemArr['image'] = isset($itemArr['image']) ? json_decode($itemArr['image'], true) : [];
                    $data[] = $itemArr;
                }
            }
        }

        return $data;
    }

    public function updateShowroom(int $id, array $data, string $property = 'image'): ?array
    {

        $showroom = $this->model->where('showrooms_id', '=', $id)->first();

        if (!$showroom) {
            return null;
        }
        // Handle image upload (replace old if new uploaded)
        if (isset($data['files'])) {
            if (is_string($data['files']) && $data['files'] !== '') {
                $this->deleteFile($showroom->$property);
                $data[$property] = $data['files'];
            } elseif (is_array($data['files']) && count($data['files']) > 0) {
                $this->deleteFile($showroom->$property);
                $data[$property] = json_encode($data['files']);
            }
            unset($data['files']);
        }
        // echo 55; exit;
        $updatedShowroom = $showroom->update($data);
        if (!$updatedShowroom instanceof Model) {
            return null;
        }
        $updatedShowroom->data->$property = is_string($updatedShowroom->data->$property) ? json_decode($updatedShowroom->data->$property, true) : $updatedShowroom->data->$property;
        return (array) $updatedShowroom?->data ?? [];
    }

    public function getShowroomData(): ?array
    {
        /**
         * dynamic query using db
         * 
         * // $query = $this->model->select(['*']);
         * // $data = [
         * //     'data' => $query->first(),
         * //     'data2' => $this->model->findAll(),
         * // ];
         * // return $data;
         */

        // $data = self::dbData();
        $data = $this->ProjectSection->findAll();
        foreach ($data as $key => $value) {
            $imageObject = json_decode($value['image'] ?? '[]', true);
            $data[$key]['image'] = isset($imageObject[0]['objectURL']) && !empty($imageObject[0]['objectURL']) ? $imageObject[0]['objectURL'] : '/img/showroom/collaborative-Hub.png';
            $data[$key]['alt'] = isset($imageObject[0]['alt']) && !empty($imageObject[0]['alt']) ? $imageObject[0]['alt'] : 'Collaborative Hub';
            $data[$key]['label'] = $value['title'] ?? 'Collaborative Hub';
        }
        // Debug::dd($data, false);
        // Break the data into chunks of 4 items per row
        $chunks = array_chunk($data, 4);
        // nested data structure for showroom sections
        $nestedData = [];
        foreach ($chunks as $index => $chunk) {
            $nestedData[] = $chunk;
        }

        return $nestedData;
    }

    public function createShowroom($data)
    {
        $section = $this->model->create($data);
        return (array) $section;
    }

    public function deleteShowroom(int $id): bool
    {
        $showroom = $this->model->where('showrooms_id', '=', $id)->first();
        if (!$showroom) {
            return false;
        }
        $showroom->delete($showroom->showrooms_id);
        return true;
    }
    public function insertShowroomData(array $data): void
    {
        $this->db->beginTransaction();
        $this->model->insert($data['showrooms']); // Insert sections data
        $this->ProjectSection->insert($data['sections']); // Insert sections data
        $this->projectSectionProduct->insert($data['sectionProducts']); // Insert section products data
        $this->projectSectionImage->insert($data['sectionImages']); // Insert section images data
        $this->db->commit();
    }

    // real data fetch from db with relations
    public function sectionDetails_backup($showroom_slug, $slug)
    {
        $showroom = $this->model->where('slug', '=', $showroom_slug)->first();
        if (!$showroom) {
            return null;
        }
        $sectionData = $this->ProjectSection
            ->where('showroom_id', '=', $showroom->showrooms_id)
            ->where('slug', '=', $slug)
            ->with([
                'sectionProducts' => function ($query) {
                    return $query->with([
                        'product' => function ($q) {
                            return $q->select(['product_id', 'image', 'description', 'price','image_thumb'])
                                ->with([
                                    'content' => function ($q) {
                                        return $q->select(['product_id', 'name', 'title', 'slug', 'content', 'tag_line']);
                                    }
                                ]);
                        }
                    ]);
                }
            ])
            ->first();
        $results = (array) $sectionData->data;
        unset($results['sectionProducts']);
        unset($results['sectionImages']);


        $sectionProductIds = $this->projectSectionProduct->where('section_id', '=', $sectionData->project_sections_id)->select(['product_id'])->orderBy('sort_order', 'ASC')->findAll(false);
        $sectionProductIds = array_column($sectionProductIds, 'product_id');
        // $productsTags = $this->taxonomyItem
        //     ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
        //     ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        //     ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        //     ->whereIn('product_to_taxonomy_item.product_id', $sectionProductIds)
        //     // ->where('taxonomy.type', '=', 'tags')
        //     ->where('taxonomy.post_type', '=', 'product')
        //     ->where('taxonomy.site_id', '=', 1)
        //     ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item.name', 'product_to_taxonomy_item.product_id', 'taxonomy_item_content.slug'])
        //     ->findAll();

        //     $productsTagsSlugs  = array_column($productsTags, 'slug', 'product_id');
        $products = $this->ProductRepository->getProductsByShowroomSectionProductIds($sectionProductIds);
        $productsData = [];
        foreach ($products as $key => $value) {
            $productsData[$value['id']]['product_url'] = $value['product_url'] ?? '';
            $productsData[$value['id']]['product_id'] = $value['id'];
            $productsData[$value['id']]['tags'] = isset($value['tags']) ? $value['tags'] : [];
            $productsData[$value['id']]['finishes'] = isset($value['finishes']) ? $value['finishes'] : [];
        }

        // product option
        // $productsOptions = [];
        // $productOptionData = $this->productOption
        //  ->whereIn('product_option.product_id', $sectionProductIds);
        //  return $productOptionData;
        //  join with 

        $sectionProducts = json_decode($sectionData->data->sectionProducts, true) ?: [];
        $sectionProductsById = [];
        foreach ($sectionProducts as $sectionProduct) {
            if (!empty($sectionProduct['product_id'])) {
                $sectionProductsById[$sectionProduct['product_id']] = $sectionProduct;
            }
        }
        $sectionImages = $this->projectSectionImage->where('section_id', '=', $sectionData->project_sections_id)->findAll();
        $sectionImages = $sectionImages ?: [];
        $results['products'] = [];
        $results['images'] = [];
        foreach ($sectionProductIds as $productId) {
            $value = $sectionProductsById[$productId] ?? null;
            if ($value && isset($value['product_id']) && !empty($value['product_id'])) {
                $productData = $value['product'] ?? $value["`product`"];
                $productContent = $productData['content'] ?? $productData["`content`"];
                $tags = isset($productsData[$value['product_id']]['tags']) ? $productsData[$value['product_id']]['tags'] : [];
                // $finishes = isset($productsData[$value['product_id']]['finishes']) ? $productsData[$value['product_id']]['finishes'] : [];
                $finishes = isset($value['finish_material']) ? $value['finish_material'] : '';

                $finishArray = [];
                
                if (!empty($finishes)) {
                
                    // Split by comma followed by another section (Carcase:, Top:, Type: ...)
                    preg_match_all('/([A-Za-z ]+)\s*:\s*([^,]+(?:,(?!\s*[A-Za-z ]+\s*:)[^,]+)*)/', $finishes, $matches, PREG_SET_ORDER);
                
                    foreach ($matches as $match) {
                
                        $title = trim($match[1]);
                        $items = array_map('trim', explode(' - ', trim($match[2])));
                
                        $finishArray[] = [
                            'title' => $title,
                            'items' => $items
                        ];
                    }
                }

                $product = [
                    'id' => $value['product_id'],
                    'tag_line' => isset($productContent) ? ($productContent['tag_line'] ?? '') : '',
                    'price' => isset($productContent) ? ($productContent['price'] ?? $productData['price'] ?? 159.48) : ($productData['price'] ?? 159.48),
                    'name' => isset($productContent) ? ($productContent['title'] ?? $productContent['name'] ?? '') : '',
                    'image' => $productData['image'][0]['objectURL'] ?? '',
                    'image_thumb' => $productData['image_thumb'][0]['objectURL'] ?? '',
                    'description' => $productData['description'] ?? $productContent['content'] ?? '',
                    'slug' => $productContent['slug'] ?? '',
                    'product_url' => $productsData[$value['product_id']]['product_url'] ?? '',  
                    // 'fabric_image' => isset($finishes[0]['finish_image']) ? $finishes[0]['finish_image'] : '/media/design-resource/finishes/finishes_logo.webp',
                    'tags' => $tags,
                    'finishes' => $finishArray,
                ];
                $results['products'][] = $product;
            }
        }
        foreach ($sectionImages as $key => $value) {
            $class = $this->getMasonryClass($key + 1);
            if (isset($value['image_link']) && !empty($value['image_link'])) {
                $img = isset($value['image']) && is_string($value['image']) ? json_decode($value['image'], true) : ($value['image'] ?? []);
                $image = [
                    'src' => $img[0]['objectURL'] ?? '',
                    'title' => $img[0]['alt'] ?? '',
                    'class' => $class,
                ];
                $results['images'][] = $image;
            }
        }
        return $results;
    }
    
    public function sectionDetails($showroom_slug, $slug)
    {
        $showroom = $this->model->where('slug', '=', $showroom_slug)->first();
        if (!$showroom) {
            return null;
        }
        $sectionData = $this->ProjectSection
            ->where('showroom_id', '=', $showroom->showrooms_id)
            ->where('slug', '=', $slug)
            ->with([
                'sectionProducts' => function ($query) {
                    return $query->with([
                        'product' => function ($q) {
                            return $q->select(['product_id', 'image', 'description', 'price','image_thumb'])
                                ->with([
                                    'content' => function ($q) {
                                        return $q->select(['product_id', 'name', 'title', 'slug', 'content', 'tag_line']);
                                    }
                                ]);
                        }
                    ]);
                }
            ])
            ->first();
        $results = (array) ($sectionData->data ?? []);
        unset($results['sectionProducts']);
        $sectionProducts = json_decode($sectionData->data->sectionProducts, true) ?: [];

        if (!empty($sectionProducts)) {
            usort($sectionProducts, function ($a, $b) {
                $orderA = isset($a['sort_order']) ? (int)$a['sort_order'] : 0;
                $orderB = isset($b['sort_order']) ? (int)$b['sort_order'] : 0;
                
                return $orderA <=> $orderB; // Ascending Order
            });
        }

        // prepareation product tag and product url
        $sectionProductIds = array_column($sectionProducts, 'product_id');
        $products = $this->ProductRepository->getProductsByShowroomSectionProductIds($sectionProductIds);
        $productsExtraData = [];
        if (!empty($products)) {
            foreach ($products as $pData) {
                if (isset($pData['id'])) {
                    $productsExtraData[$pData['id']] = $pData;
                }
            }
        }
        // end 
        $results['products'] = [];
        $results['images'] = [];
        if (!empty($sectionProducts)) {
            foreach ($sectionProducts as $sectionProduct) {
                $productData = $sectionProduct['product'] ?? $sectionProduct['`product`'] ?? null;
                
                if (!$productData) {
                    continue; 
                }
                $currentProductId = $productData['product_id'] ?? $sectionProduct['product_id'] ?? null;
                $productContent = $productData['content'] ?? $productData['`content`'] ?? null;

                $finishArray = [];
                $finishes = $sectionProduct['finish_material'] ?? '';
                if (!empty($finishes)) {
                    preg_match_all('/([A-Za-z ]+)\s*:\s*([^,]+(?:,(?!\s*[A-Za-z ]+\s*:)[^,]+)*)/', $finishes, $matches, PREG_SET_ORDER);
                    foreach ($matches as $match) {
                        $finishArray[] = [
                            'title' => trim($match[1]),
                            'items' => array_map('trim', explode(' - ', trim($match[2])))
                        ];
                    }
                }
    
                // product url and tags
                $extraData = $productsExtraData[$currentProductId] ?? null;
                $results['products'][] = [
                    'id'          => $currentProductId,
                    'tag_line'    => $productContent['tag_line'] ?? '',
                    'price'       => $productContent['price'] ?? $productData['price'] ?? 159.48,
                    'name'        => $productContent['title'] ?? $productContent['name'] ?? '',
                    'image'       => $productData['image'][0]['objectURL'] ?? '',
                    'image_thumb' => $productData['image_thumb'][0]['objectURL'] ?? $productData['image'][0]['objectURL'] ?? '',
                    'description' => $productData['description'] ?? $productContent['content'] ?? '',
                    'slug'        => $productContent['slug'] ?? '',
                    'finishes'    => $finishArray,
                    // from product
                    'product_url' => $extraData['product_url'] ?? '', 
                    'tags'        => $extraData['tags'] ?? [],
                ];
            }
        }
        
        $sectionImages = $this->projectSectionImage->where('section_id', '=', $sectionData->project_sections_id)->findAll();
        $sectionImages = $sectionImages ?: [];
        foreach ($sectionImages as $key => $value) {
            $class = $this->getMasonryClass($key + 1);
            if (isset($value['image_link']) && !empty($value['image_link'])) {
                $img = isset($value['image']) && is_string($value['image']) ? json_decode($value['image'], true) : ($value['image'] ?? []);
                $image = [
                    'src' => $img[0]['objectURL'] ?? '',
                    'title' => $img[0]['alt'] ?? '',
                    'class' => $class,
                ];
                $results['images'][] = $image;
            }
        }
        return $results;
    }

    public function getMasonryClass(int $imageCount): string
    {
        // Pattern repeats every 6 images:
        // 1: 16-6, 2: 8-4, 3: 8-6, 4: 8-6, 5: 8-4, 6: 16-6
        $position = $imageCount % 6;
        if ($position == 0)
            $position = 6;
        $class = "th-masonry-img-item";

        switch ($position) {
            case 1:
            case 6:
                $class .= ' th-masonry-img-item-16-6';
                break;
            case 2:
            case 5:
                $class .= ' th-masonry-img-item-8-4';
                break;
            case 3:
            case 4:
                $class .= ' th-masonry-img-item-8-6';
                break;
            default:
                $class .= ' th-masonry-img-item-16-6';
        }
        return $class;
    }

    public function findById($id)
    {
        // Fetch the record
        $query = $this->model->where('showrooms_id', '=', $id)->first();

        if (!$query) {
            return null; // not found
        }
        return $query->data;
    }
    public function findBySlug($slug)
    {
        // Fetch the record
        $query = $this->model->where('slug', '=', $slug)->first();

        if (!$query) {
            return null; // not found
        }
        return $query->data;
    }
    public function findSectionById($id)
    {
        // Fetch the record
        $query = $this->ProjectSection->where('project_sections_id', '=', $id)->first();

        if (!$query) {
            return null; // not found
        }
        return $query->data;
    }
    public function findShowroomSectionById($id)
    {
        // Fetch the record
        $result = $this->ProjectSection->where('showroom_id', '=', $id)->findAll();

        if (!$result) {
            return null; // not found
        }
        foreach ($result as $key => $value) {
            $result[$key]['image'] = isset($value['image']) && is_string($value['image']) ? json_decode($value['image'], true) : [];
        }
        return $result;
    }
    //***************************** Make sure you pass showroom slug in showroomDetails method

    public function showroomDetails()
    {

        //***************************** Retrive only one showroom here with its sections
        $showrooms = $this->model->with(['projectSection'])->findAll();

        //***************************** Retrive all showroom section products here 
        $sectionProducts = $this->projectSectionProduct->findAll();

        //Retrive all showroom section images here 
        $sectionImages = $this->projectSectionImage->findAll();

        $results = [];

        foreach ($showrooms as $showroom) {
            // Convert JSON
            $projectSections = json_decode($showroom['projectSection'] ?? '[]', true) ?: [];

            $showroomData = [
                'showrooms_id' => $showroom['showrooms_id'],
                'title' => $showroom['title'],
                'slug' => $showroom['slug'],
                'description' => $showroom['description'],
                'image' => $showroom['image'], // keep as JSON string
                'status' => $showroom['status'],
                'projectSection' => [],
            ];

            // Iterate through each section
            foreach ($projectSections as $section) {
                $sectionId = $section['project_sections_id'];

                // section products
                $sectionProducts = $this->projectSectionProduct
                    ->where('section_id', '=', $sectionId)
                    ->with([
                        'product' => function ($q) {
                            $q->select(['product_id', 'image', 'description', 'price'])
                                ->with([
                                    'content' => function ($c) {
                                        $c->select(['product_id', 'name', 'slug', 'content']);
                                    }
                                ]);
                        }
                    ])
                    ->findAll(false);

                // product tag
                // $productIds = array_column($sectionProducts, 'product_id');
                // $productsTagsData = $this->taxonomyItem
                //     ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
                //     ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
                //     ->whereIn('product_to_taxonomy_item.product_id', $productIds)
                //     ->where('taxonomy.type', 'tags')
                //     ->where('taxonomy.post_type', 'product')
                //     ->where('taxonomy.site_id', '=', 1)
                //     ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item.name', 'product_to_taxonomy_item.product_id'])
                //     ->findAll();

                // // Format tags per product
                // $productsTags = [];
                // foreach ($productsTagsData as $tag) {
                //     $productsTags[$tag['product_id']][] = $tag['name'];
                // }

                // Format products
                $formattedProducts = [];
                foreach ($sectionProducts as $sp) {
                    $productData = $sp['product'] ?? [];
                    $content = $productData['content'] ?? [];

                    $formattedProducts[] = [
                        'id' => $sp['product_id'],
                        'price' => $productData['price'] ?? 0,
                        'name' => $content['name'] ?? '',
                        'image' => $productData['image'][0]['objectURL'] ?? '',
                        'description' => $productData['description'] ?? $content['content'] ?? '',
                        'slug' => $content['slug'] ?? '',
                        'fabric_image' => '/img/showroom/details/list1.png',
                        'tags' => $productsTags[$sp['product_id']] ?? [],
                    ];
                }

                // section images
                $sectionImages = $section['image'] ?? [];
                $formattedImages = [];
                foreach ($sectionImages as $key => $img) {
                    $formattedImages[] = [
                        'src' => $img['objectURL'] ?? $img['image'] ?? '',
                        'title' => $img['alt'] ?? '',
                        'class' => $this->getMasonryClass($key + 1),
                    ];
                }

                $showroomData['projectSection'][] = [
                    'project_sections_id' => $sectionId,
                    'slug' => $section['slug'] ?? '',
                    'image' => $section['image'] ?? null,
                    'title' => $section['title'] ?? '',
                    'status' => $section['status'] ?? '',
                    'projectProducts' => $formattedProducts,
                    'projectImages' => $formattedImages,
                ];
            }

            $results[] = $showroomData;
        }

        return $results;
    }


    public function addSection(string $id, array $data): ?array
    {
        $showroom = $this->model->where('showrooms_id', '=', $id)->first();
        if (!$showroom) {
            return null;
        }

        // unique section code 
        $existing = $this->ProjectSection
            ->where('section_code', '=', $data['section_code'])
            ->first();

        if ($existing) {
            //Return something
            return ['duplicate' => true];
        }
        $section = $this->ProjectSection->create([
            'showroom_id' => $showroom->showrooms_id,
            'section_code' => $data['section_code'],
            'title' => $data['title'],
            'slug' => makeSlug($data['slug']),
            'description' => $data['description'],
            'status' => $data['status'] ?? 'active',
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return (array) $section->data;
    }
    public function updateSection(int $id, array $data): ?array
    {
        $section = $this->ProjectSection->where('project_sections_id', '=', $id)->first();
        if (!$section) {
            return null;
        }
        if (isset($data['files'])) {
            if (is_string($data['files']) && $data['files'] !== '') {
                $this->deleteFile($section->image);
                $data['image'] = $data['files'];
            } elseif (is_array($data['files']) && count($data['files']) > 0) {
                $this->deleteFile($section->image);
                $data['image'] = json_encode($data['files']);
            }
            unset($data['files']);
        }

        $section->update($data);
        if ($section->data?->image) {
            $section->data->image = is_string($section->data?->image) ? json_decode($section->data?->image, true) : $section->data?->image;
        }
        return (array) $section->data;
    }
    public function deleteSection(int $id): bool
    {
        $section = $this->ProjectSection->where('project_sections_id', '=', $id)->first();
        if (!$section) {
            return false;
        }
        $section->delete($section->project_sections_id);
        return true;
    }
    public function sectionImages(int $id): ?array
    {
        $sectionImages = $this->projectSectionImage->where('section_id', '=', $id)->findAll(false);
        if (!$sectionImages) {
            return [];
        }
        foreach ($sectionImages as $key => $value) {
            $sectionImages[$key]['image'] = is_string($value['image']) ? json_decode($value['image'], true) : $value['image'];
            $sectionImages[$key]['status'] = is_string($value['status']) ? json_decode($value['status'], true) : $value['status'];
        }
        return $sectionImages;
    }
    public function addSectionImages(array $data): bool
    {
        try {
            $this->projectSectionImage->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function addSectionImage(int $id, array $data): ?array
    {
        $section = $this->ProjectSection->where('project_sections_id', '=', $id)->first();
        if (!$section) {
            return null;
        }
        return (array) $section->data;
    }
    public function updateSectionImage(int $id, array $data): ?array
    {
        $section = $this->ProjectSection->where('project_sections_id', '=', $id)->first();
        if (!$section) {
            return null;
        }
        //Need to check Old files and make sure to delete them

        return (array) $section->data;
    }
    public function sectionProducts(int $id): ?array
    {
        $this->projectSectionProduct->clearQuery();
        $sectionProducts = $this->projectSectionProduct
            ->where('section_id', '=', $id)
            ->with([
                'product' => function ($q) {
                    return $q->select(['product_id', 'image', 'description', 'price'])
                        ->with([
                            'content' => function ($c) {
                                return $c->select(['product_id', 'name', 'slug', 'content']);
                            }
                        ]);
                }
            ])
            ->orderBy('sort_order', 'ASC')
            ->findAll(false);
        if (!$sectionProducts) {
            return null;
        }
        foreach ($sectionProducts as $key => $value) {
            $sectionProducts[$key]['product'] = is_string($value['product']) ? json_decode($value['product'], true) : $value['product'];
            $sectionProducts[$key]['status'] = is_string($value['status']) ? json_decode($value['status'], true) : $value['status'];
        }
        return $sectionProducts;
    }
    public function addSectionProduct(int $id, int $productId): ?array
    {
        $sortOrder = $this->projectSectionProduct->where('section_id', '=', $id)->countAll();
        $this->projectSectionProduct->insert([
            [
                'section_id' => $id,
                'product_id' => $productId,
                'sort_order' => $sortOrder + 1,
                'status' => '{"active": true}',
            ]
        ]);

        return $this->sectionProducts($id);
    }
    public function updateSectionProduct(int $id, array $data): ?array
    {
        $section = $this->ProjectSection->where('project_sections_id', '=', $id)->first();
        if (!$section) {
            return null;
        }
        return (array) $section->data;
    }
    public function deleteSectionProduct(int $id, int $project_section_products_id): bool
    {
        try {
            $this->projectSectionProduct->deleteWhere(['project_section_products_id' => $project_section_products_id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteSectionImage(int $id, int $imageId): bool
    {
        try {
            // Fetch the image record
            $sectionImage = $this->projectSectionImage
                ->where('project_section_images_id', '=', $imageId)
                ->first();

            if (!$sectionImage) {
                return false;
            }
            // Delete DB record
            $deleted = $this->projectSectionImage->deleteWhere(['project_section_images_id' => $imageId]);
            if ($deleted) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $sectionImage->image_link;

                // Delete old image
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log('Failed to delete section image: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteImageByProperty(int $showroom_id, string $property, string $type='showroom'): bool
    {
        if($type === 'section'){
            $section = $this->ProjectSection->where('project_sections_id', '=', $showroom_id)->first();
            if (!$section) {
                return false;
            }
            $section->update([$property => json_encode([])]);
        }else{
            $showroom = $this->model->where('showrooms_id', '=', $showroom_id)->first();
            if (!$showroom) {
                return false;
            }
            $images = json_decode($showroom->$property, true);
            $this->deleteFile($images);
    
            //Delete Media record from media table using path
            foreach ($images as $img) {
                $this->mediaRepository->deleteMediaByPath($img['objectURL']);
            }
            $this->model->update([$property => json_encode([])]);
        }

        return true;
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

    public function getShowroomsComponentData($param = [], $links = [])
    {
        $model = $this->model; // Your model instance

        if (isset($param['model']) && $param['model'] === 'showrooms') {

            // Select fields
            if (isset($param['fields']) && is_array($param['fields'])) {
                $model->select(array_merge($param['fields'], ['is_section_active', 'google_map_link']));
            }

            // Order by recent if requested
            if (!empty($param['is_recent']) && $param['is_recent'] === true) {
                $model->orderBy('created_at', 'DESC');
            }

            // Limit number of items if provided
            if (!empty($param['item_count']) && $param['item_count'] > 0) {
                $model->limit($param['item_count']);
            }

            $href_link = "";

            $showroomIds = [
                'Sydney' => 1, 
                'Melbourne' => 2, 
                'Brisbane' => 3, 
                // lowercase showroom names
                'sydney' => 1, 
                'melbourne' => 2, 
                'brisbane' => 3
            ];
           
            // Fetch results
            $results = $model->findAll();
            foreach ($results as $key => $item) {
                $href_link = "";
                $explore_btn = "";
                foreach ($links as $link) {
                    // $parts = explode('-', $link['title']);
             
                    $showroom_id_from_link = $showroomIds[$link['title']];
                    $explore_btn = "Explore Virtually";
                    // Match with DB showroom id
                    if ($showroom_id_from_link === (int) $item['showrooms_id']) {
                        $href_link = $link['url'];
                        break;
                    }
                }
                $image = json_decode($item['image']);
                $results[$key]['image'] = $image[0]->objectURL ?? '';
                $results[$key]['book_btn'] = "Book Now";
                $results[$key]['book_link'] = "/contact-sales?showroom=" . $item['showrooms_id'] . "#book-now";
                $results[$key]['map_link'] = $item['google_map_link'] ?? '';
                $results[$key]['view_btn'] = $explore_btn;
                $results[$key]['explore_link'] = $href_link;
                $results[$key]['view_link'] = $item['is_section_active'] == 1 ? "/showroom" . "/" . $item['slug'] : "#";
            }

            return $results;
        }

        return [];
    }

    public function getShowroomComponentData($param = []): array
    {
        $query = $this->model; // Your model instance

        if (isset($param['model']) && $param['model'] === 'showrooms' && isset($param['slug']) && $param['slug']) {

            $query->where('slug', '=', $param['slug']);
            if (isset($param['join']) && is_array($param['join'])) {
                foreach ($param['join'] as $join) {
                    $query->join($join['table'], $join['on'], $join['type']);
                }
            }
            // Select fields
            if (isset($param['fields']) && is_array($param['fields'])) {
                $query->select($param['fields']);
            }
            // Fetch results
            $showroom = $query->first();
            if (!$showroom) {
                return [];
            }
            $results = (array) $showroom->data;
            $image = json_decode($results['image']);
            $results['image'] = $image[0]->objectURL ?? '';
            $results['alt'] = $image[0]->name ?? '';
            $overviewImage = json_decode($results['overview_image']);
            if (isset($overviewImage[0])) {
                $results['overview_image'] = $overviewImage[0]->objectURL ?? '';
                $results['overview_alt'] = $overviewImage[0]->name ?? '';
            }
            if (isset($results['banner_image'])) {
                $bannerImage = json_decode($results['banner_image']);
                if (isset($bannerImage[0])) {
                    $results['banner_image'] = $bannerImage[0]->objectURL ?? '';
                    $results['banner_alt'] = $bannerImage[0]->name ?? '';
                }
            }
            $results['sections'] = $this->ProjectSection->where('showroom_id', "=", $showroom->showrooms_id)->orderBy('title', 'ASC')->findAll();
            $sections = [];
            $index = 0;
            foreach ($results['sections'] as $key => $section) {
                $image = json_decode($section['image']);
                if (isset($image[0])) {
                    $section['image'] = $image[0]->objectURL ?? '';
                    $section['alt'] = $image[0]->name ?? '';
                }
                $sections[$index][] = $section;
                if ((($key + 1) % 4) === 0) {
                    $index++;
                }
            }
            $results['sections'] = $sections;
            $results['way_points'] = isset($showroom->data->banner_way_points) ? json_decode($showroom->data->banner_way_points, true) : [];

            return $results;
        }

        return [];
    }
    public function importSectionsImages(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validImages = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingImages = [];
        $mediaData = [];
        $defaultFields = $this->getDefaultSectionImageFields($headers);
        $requiredFields = [
            'section_id',
            'image_link',
            'image',
            'showroom_id',
            'section_id',
        ];

        // Pre-fetch all products for mapping        
        $sectionImages = $this->projectSectionImage->select(['project_section_images_id', 'section_id', 'image_link'])->findAll();
        
        $sections = $this->ProjectSection->select(['showroom_id', 'project_sections_id', 'section_code'])->limit(0)->findAll(false);
        $sectionIds = [];
        foreach ($sections as $row) {
            $sectionIds[$row['section_code'].'-'.$row['showroom_id']] = (int)$row['project_sections_id'];
        }

        $sectionImagesMap = [];
        foreach ($sectionImages as $image) {
            $sectionImagesMap[$image['section_id']][$image['image_link']] = $image['project_section_images_id'];
        }

        // $config = env('APP_URL');
        // $imageServer = $config; // $config['APP_URL'];

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);
                if(!isset($record['showroom_id']) || empty($record['showroom_id'])){
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['showroom_id' => 'Showroom ID is required']
                    ];
                }

                $validator = new ShowroomSectionImageValidation($record, $requiredFields, array_keys($defaultFields), $sectionImagesMap, $sectionIds);
                $validated = $validator->validate();
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                }
                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }
                $sectionImage = $validator->toArray();

                if ($validator->isExistingData) {
                    $existingImages[] = $sectionImage;
                } else {
                    $validImages[] = $sectionImage;
                }
                if(isset($validated->media) && !empty($validated->media)){
                    $mediaData[] = (array) $validated->media;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        $insertedCount = 0;
        if(!empty($mediaData)){
            $this->media->upsert($mediaData, ['meta']);
            $imageLinks = array_column($mediaData, 'meta');
            $this->media->upsert($mediaData, ['path']);
            $mediaIds = $this->media->whereIn('meta', $imageLinks)->select(['meta', 'media_id'])->limit(0)->findAll();
            $mediaIdsMap = array_column($mediaIds, 'media_id', 'meta');
        }
        try {
            $this->db->beginTransaction();
            if (count($validImages) > 0) {
                foreach($validImages as $key => $image){
                    // Fix Undefined array key "image_link"
                    $imageLink = isset($image['image_link']) ? $image['image_link'] : null;
                    if ($imageLink !== null && isset($mediaIdsMap[$imageLink])) {
                        $validImages[$key]['media_id'] = $mediaIdsMap[$imageLink];
                    } else {
                        $validImages[$key]['media_id'] = null;
                    }
                }
                $insertedCount = $this->projectSectionImage->upsert($validImages, ['section_id', 'image_link']);
            }
            if (count($existingImages) > 0) {
                foreach($existingImages as $key => $image){
                    if(isset($mediaIdsMap[$image['image_link']])){
                        $existingImages[$key]['media_id'] = $mediaIdsMap[$image['image_link']];
                    }else{
                        $existingImages[$key]['media_id'] = null;
                    }
                }
                $this->projectSectionImage->upsert($existingImages, ['section_id', 'image_link']);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert section images: " . $e->getMessage());
        }

        return [
            'success' => true,
            'valid_records' => count($validImages),
            'valid_data' => $validImages,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'updated_records' => count($existingImages),
            'updated_data' => $existingImages,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'inserted_count' => $insertedCount,
        ];
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function getDefaultSectionImageFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) {
            $defaults[$h] = null;
        }

        // Set defaults for product_image table (based on migration)
        $defaults['image'] = json_encode([]);
        $defaults['sort_order'] = 0;
        $defaults['status'] = json_encode(['active' => true]);
        // $defaults['way_points'] = json_encode([]);
        // project_section_images_id 
        // section_id Index	
        // image_link Index	
        // image	json		
        // media_id
        // status	json		
        // sort_order

        return $defaults;
    }

    public function importSectionProducts(string $csvFilePath): array
    {
        $reader = Reader::createFromPath($csvFilePath, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validSectionProducts = [];
        $existingSectionProducts = [];
        $validProductCode = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultSectionProductFields($headers);
        $requiredFields = [
            'section_id',
            'product_code'
        ];

        // Pre-fetch all products for mapping
        $allProducts = $this->Product->select(['product_id', 'product_code'])->limit(0)->findAll();
        $productCodeMap = array_column($allProducts, 'product_id', 'product_code');
        // section mapping
        $sections = $this->ProjectSection->select(['project_sections_id', 'section_code', 'showroom_id'])->limit(0)->findAll(false);
        $sectionIdMap = [];
        foreach ($sections as $row) {
            $sectionIdMap[$row['section_code'].'-'.$row['showroom_id']] = (int)$row['project_sections_id'];
        }

        // section product mapping
        $allSectionProducts = $this->projectSectionProduct->select(['project_section_products_id', 'section_id', 'product_id'])->limit(0)->findAll(false);
        $exsitingSectionProductMap = [];
        foreach ($allSectionProducts as $product) {
            $exsitingSectionProductMap[$product['section_id'] . "-" . $product['product_id']] = $product['project_section_products_id'];
        }

        $exstingMaps = [
            'productIdsMap' => $productCodeMap,
            'sectionIdsMap' => $sectionIdMap
        ];

        foreach ($records as $offset => $record) {
            try {
                $record = array_merge($defaultFields, $record);
                $validator = new ShowroomSectionProductValidation($record, $requiredFields, array_keys($defaultFields), $exstingMaps, $exsitingSectionProductMap);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }
                $sectionProduct = $validator->toArray();
                if ($validator->isExistingData) {
                    $existingSectionProducts[] = $sectionProduct;
                } else {
                    $validSectionProducts[] = $sectionProduct;
                }
                
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
            }
        }

        try {
            $this->db->beginTransaction();
            if (count($validSectionProducts) > 0 && !empty($validSectionProducts)) {
                $this->projectSectionProduct->upsert($validSectionProducts, ['product_id', 'section_id']);
            }
            if (count($existingSectionProducts) > 0 && !empty($existingSectionProducts)) {
               $this->projectSectionProduct->upsert($existingSectionProducts, ['product_id', 'section_id']);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert related products: " . $e->getMessage());
        }

        return [
            'success' => true,
            'valid_records' => count($validSectionProducts),
            'invalid_records' => count($invalid),
            'updated_records' => count($existingSectionProducts),
            'inserted_count' => count($validSectionProducts),
            'updated_count' => count($existingSectionProducts),
            'updated_data' => $existingSectionProducts,
            'product_ids_map' => $productCodeMap,
            'section_ids_map' => $sectionIdMap,
            'valid_data' => $validSectionProducts,
            'invalid_data' => $invalid,
            'existing_count' => count($existingSectionProducts),
            'existing_data' => $existingSectionProducts
        ];
    }

    /**
     * Get default fields for related products import
     */
    private function getDefaultSectionProductFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) {
            $defaults[$h] = null;
        }

        // Set defaults for related products table
        $defaults['product_code'] = '';
        $defaults['slug'] = '';
        $defaults['finish_material'] = '';
        $defaults['status'] = json_encode(['active' => true]);

        return $defaults;
    }

    // import csv
    public function importSections(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validSections = [];
        $validSectionContents = [];
        $existingSections = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        // $categories = $this->getCategoriesForValidation();
        $defaultFields = $this->getDefaultFields($headers);
        $sectionMapping = $this->ProjectSection->select(['section_code', 'project_sections_id', 'showroom_id'])->findAll(false);
        $sectionMaps = [];
        foreach ($sectionMapping as $row) {
            $sectionMaps[$row['section_code'].'-'.$row['showroom_id']] = (int)$row['project_sections_id'];
        }

        foreach ($records as $offset => $record) {
            try {
                if (!isset($record['showroom_id']) || empty($record['showroom_id'])) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['showroom_id' => "showroom code is required"]
                    ];
                    continue;
                }

                $record = isset($record['showroom_id']) && $record['showroom_id'] ? $record : array_merge($defaultFields, $record);

                $validator = new ShowroomDataValidation($record, array_keys($defaultFields), $sectionMaps);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }

                // Extract data directly from validated stdClass objects
                $section = (array) $validated->toArray();
                if (count($section) > 0) {
                    if ($validated->isExistingData) {
                        $existingSections[] = $section;
                    } else {
                        $validSections[] = $section;
                    }
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        // $insertResult = $this->insertSections($validSections, $validSectionContents);
        if (count($validSections) > 0) {
            $insertResult = $this->ProjectSection->upsert($validSections, ['showroom_id', 'project_id']);
        }
        if (count($existingSections) > 0) {
            $updateResult = $this->ProjectSection->upsert($existingSections, ['showroom_id', 'project_id']);
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validSections),
            'invalid_records' => count($invalid),
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'updated_records' => count($existingSections),
            'updated_data' => $existingSections,
            'inserted_count' => count($validSections),
            'valid_data' => $validSections,
            'invalid_data' => $invalid,
        ];
    }
    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];

        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['showroom_id'] = null;
        $defaultFields['project_id'] = null;
        $defaultFields['title'] = 'test';
        $defaultFields['description'] = 'description';
        $defaultFields['status'] = 'show'; // feild type is varchar //  json_encode(['active' => true]);
        $defaultFields['sort_order'] = 1;
        return $defaultFields;
    }

    private function insertSections(array $products, array $contents): array
    {
        $insertedCount = 0;
        $insertedContentCount = 0;
        $codeToId = [];

        if (empty($products)) {
            return ['inserted_count' => 0, 'inserted_content_count' => 0];
        }

        try {
            $this->db->beginTransaction();

            // STEP 1: Pre-fetch existing product IDs
            $sectionCodes = array_column($products, 'showroom_id');
            $existingProducts = $this->ProjectSection->select(['project_sections_id', 'showroom_id'])
                ->whereIn('showroom_id', $sectionCodes)
                ->findAll();

            // Build existing mapping
            foreach ($existingProducts as $row) {
                $codeToId[$row['showroom_id']] = (int)$row['showroom_id'];
            }

            // STEP 2: Separate new vs existing products
            $newSections = [];
            $existingSectionCodes = array_keys($codeToId);

            foreach ($products as $product) {
                if (!in_array($product['showroom_id'], $existingSectionCodes)) {
                    $newSections[] = $product;
                }
            }

            // STEP 3: Insert only new products
            if (!empty($newSections)) {
                $this->ProjectSection->insert($newSections);

                // STEP 4: Get IDs for newly inserted products
                // This should work because we're querying after the insert within the same transaction
                $newSectionCodes = array_column($newSections, 'showroom_id');
                $newSectionIds = $this->ProjectSection->select(['project_sections_id', 'showroom_id'])
                    ->whereIn('showroom_id', $newSectionCodes)
                    ->findAll();

                foreach ($newSectionIds as $row) {
                    $codeToId[$row['project_sections_id']] = (int)$row['showroom_id'];
                }
            }

            // STEP 5: Update existing products
            $updateSections = array_filter($products, function ($product) use ($existingSectionCodes) {
                return in_array($product['showroom_id'], $existingSectionCodes);
            });
            $updateSections = array_values($updateSections);

            if (!empty($updateSections)) {
                $this->ProjectSection->upsert($updateSections, ['showroom_id']);
            }

            $insertedCount = count($newSections) + count($updateSections);

            // STEP 6: Process contents with all product IDs now available
            // if (!empty($contents)) {
            //     $finalContentData = [];
            //     foreach ($contents as $index => $content) {
            //         $productCode = $products[$index]['product_code'] ?? null;
            //         if ($productCode && isset($codeToId[$productCode])) {
            //             $content['product_id'] = $codeToId[$productCode];
            //             $finalContentData[] = $content;
            //         }
            //     }

            //     if (!empty($finalContentData)) {
            //         $insertedContentCount = $this->productContent->upsert($finalContentData, ['product_id', 'language_id']);
            //     }
            // }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert products: " . $e->getMessage());
        }

        return ['inserted_count' => $insertedCount, 'inserted_content_count' => $insertedContentCount, 'productIds' => $codeToId];
    }

    public function getShowroomForPinboard(): ?array
    {
        $query = $this->model;
        $query = $query->select(['showrooms_id', 'title', 'address','image'])->findAll();
        // format with state group 
        $formattedData = [];
        foreach ($query as $row) {
            // showrooms_id, title, state group
            $image = $row['image'] ? json_decode($row['image'], true) : [];
            $objectURL = isset($image[0]) && isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
            $formattedData[] = [
                'showrooms_id' => $row['showrooms_id'],
                'title' => $row['title'],
                'address' => $row['address'],
                'group' => strtoupper(explode(' ', $row['title'])[0] ?? ''),
                'image' => $objectURL,
            ];
        }
        return $formattedData;
    }

    public function updateWayPoints(array $data): array
    {
        $model_id = $data['model_id'];
        $model_type = $data['model_type'];
        $way_points = $data['way_points'];

        $model_type = $model_type ?? 'showroom';
        $query = null;
        if($model_type == 'showroom') {
           $query = $this->model->where('showrooms_id', '=', $model_id)->first();
        }

        if (!$query) {
            return [
                'success' => false,
                'message' => 'Showroom not found'
            ];
        }
        $updatedData = $query->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully',
            'data' => $data
        ]; 
    }


    // show room contact person details
    public function getShowroomContactForComponent(int $showroom_id): array
    {
        $query = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select(['showroom_contact_id', 'showroom_id', 'image', 'name', 'email', 'phone', 'designation', 'message', 'showrooms.title', 'showroom_contact.sort_order', 'showroom_contact.status'])
        ->where('showroom_id', '=', $showroom_id)
        ->where('showroom_contact.status', '=', 1)
        ->where('showroom_contact.sales_team_contact', '=', 0)
        ->orderBy('showroom_contact.sort_order', 'asc')
        ->findAll();

        // format image data
        $formattedData = [];
        foreach ($query as $row) {
            $image = $row['image'] ? json_decode($row['image'], true) : [];
            $objectURL = isset($image[0]) && isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
         
            $formattedData[] = [
                'showroom_contact_id' => $row['showroom_contact_id'],
                'showroom_title' => $row['title'],
                'showroom_id' => $row['showroom_id'],
                'name' => $row['name'],
                'image' => $objectURL,
                'email' => $row['email'],
                'phone' => $row['phone'],
                'designation' => $row['designation'],
                'message' => $row['message'],
            ];
        }
        return $formattedData;
    }

    // list of showroom contact
    public function getShowroomContactList(): array
    {
        $query = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select(['showroom_contact_id', 'image', 'name', 'email', 'phone', 'designation', 'message', 'showrooms.title', 'showroom_contact.sort_order', 'showroom_contact.status'])
        ->orderBy('showroom_contact.showroom_id', 'asc')
        ->orderBy('showroom_contact.sort_order', 'asc')
        ->findAll();

        // format image data
        $formattedData = [];
        foreach ($query as $row) {
            $image = $row['image'] ? json_decode($row['image'], true) : [];
            $objectURL = isset($image[0]) && isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
         
            $formattedData[] = [
                'showroom_contact_id' => $row['showroom_contact_id'],
                'showroom_title' => $row['title'],
                'name' => $row['name'],
                'image' => $image,
                'email' => $row['email'],
                'phone' => $row['phone'],
                'designation' => $row['designation'],
                'message' => $row['message'],
                'sort_order' => $row['sort_order'],
                'status' => $row['status'],
            ];
        }
        return $formattedData;
    }

    // list of showroom contact by id for component
    public function getShowroomContactById(int $showroom_contact_id): array
    {
        $query = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select(['showroom_contact_id', 'showroom_id', 'image', 'name', 'email', 'phone', 'designation', 'message', 'showrooms.title', 'showroom_contact.sort_order', 'showroom_contact.status'])
        ->where('showroom_contact.showroom_contact_id', '=', $showroom_contact_id)
        ->orderBy('showroom_contact.sort_order', 'asc')
        ->first();

        // format image data
        $formattedData = $query ? (array) $query->data : [];
        $image = $formattedData['image'] ? json_decode($formattedData['image'], true) : [];
        $formattedData['image'] =$image;
        $formattedData['showroom_title'] = $formattedData['title'] ?? '';
        return $formattedData;
    }

    // delete showroom contact by id
    public function deleteShowroomContactById(int $showroom_contact_id): bool
    {
        $query = $this->showroomContact
        ->where('showroom_contact_id', '=', $showroom_contact_id)
        ->first();
        if (!$query) {
            return false;
        }
        $deleted = $query->delete($showroom_contact_id);
        if (!$deleted) {
            return false;
        }
        return true;
    }

    // create showroom contact
    public function createShowroomContact(array $data): array
    {
        $createData = [
            'showroom_id' => $data['showroom_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'designation' => $data['designation'],
            'message' => $data['message'],
            'sort_order' => $data['sort_order'],
            'status' => $data['status'] ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $showroomContact = $this->showroomContact->create($createData);
        if (!$showroomContact) {
            return ['success' => false, 'message' => 'Showroom contact creation failed'];
        }
        return (array) $showroomContact?->data ?? [];
    }

    // update showroom contact by id
    public function updateShowroomContactById(int $showroom_contact_id, array $data): array
    {
        $query = $this->showroomContact->where('showroom_contact.showroom_contact_id', '=', $showroom_contact_id)->first();
        if (!$query) {
            return ['success' => false, 'message' => 'Showroom contact not found'];
        }

        if(isset($data['image'])){
            $image = $data['image'];
            $image = json_encode($image);
            $data['image'] = $image;
        }

        $updateData = [
            'showroom_id' => $data['showroom_id'],
            'name' => $data['name'],
            'image' => $data['image'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'designation' => $data['designation'],
            'message' => $data['message'],
            'sort_order' => isset($data['sort_order']) ? (int) $data['sort_order'] : 0,
            'status' => $data['status'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $query->update($updateData);
        if (!$query) {
            return ['success' => false, 'message' => 'Showroom contact update failed'];
        }
        return $this->getShowroomContactById($showroom_contact_id);
    }

    // import showroom contact
    public function importShowroomContact(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultShowroomContactFields($headers);
        $requiredFields = [
            'showroom_id',
            'name',
            'email',
            'phone',
            'designation',
            'message'
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingData = [];
        $showExistingData = [];
        $existingShowroomContactIds = $this->showroomContact->select(['showroom_contact_id', 'name'])->limit(0)->findAll(false);
        $existingShowroomContactIds = array_column($existingShowroomContactIds, 'showroom_contact_id', 'name');

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ShowroomContactDataValidation($record, $requiredFields, array_keys($defaultFields), $existingShowroomContactIds);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->showroomContact;
                    $showExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->showroomContact;
                    $showData[] = $record;
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

        try {
            $this->db->beginTransaction();
            if (count($validData) > 0) {
                $this->showroomContact->insert($validData);
            }

            if (count($existingData) > 0) {
                $this->showroomContact->upsert($existingData, ['showroom_contact_id']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update manufacturers: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $showData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($existingData),
            'duplicated_data' => $showExistingData,
            'manufacturers' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'manufacturer_processed' => count($validData),
                'manufacturer_records_created' => $validData,
                'errors' => count($invalid),
            ],
            'mapping_data' => [],
        ];
    }

    private function getDefaultShowroomContactFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['showroom_id'] = 1;
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['manufacturer_code']) && $record['manufacturer_code'] ? $record : array_merge($defaultFields, $record);
    }

    public function updateShowroomContactImage(int $showroom_contact_id, array $data): bool
    {
        $showroomContact = $this->showroomContact->where('showroom_contact_id', '=', $showroom_contact_id)->first();
        if (!$showroomContact) {
            return false; // manufacturer not found
        }
        $dataobj = $data;

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'showroom_contact_id' => $showroom_contact_id,
                'name' => $item['name'] ?? '',
                'size' => $item['size'] ?? '',
                'type' => $item['type'] ?? '',
                'image' => $item['image'] ?? '',
                'status' => isset($item['status']) && is_array($item['status'])
                    ? $item['status']
                    : ['name' => 'Uploaded', 'severity' => 'success'],
                'media_id' => $item['media_id'] ?? null,
                'objectURL' => ($item['objectURL'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $item['description'] ?? '',
                'showroom_contact_image_id' => $showroom_contact_id,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            // UPDATE `showroom_contact` SET `image` = $img WHERE `showroom_contact`.`showroom_contact_id` = $showroom_contact_id
            $showroomContact->update(['image' => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete showroom contact image by id
    public function deleteShowroomContactImage(int $showroom_contact_id): bool
    {
        $showroomContact = $this->showroomContact->where('showroom_contact_id', '=', $showroom_contact_id)->first();
        if (!$showroomContact) {
            return false;
        }
        $deleted = $showroomContact->update(['image' => null]);
        if (!$deleted) {
            return false;
        }
        return true;
    }

    public function getMembersData($showroom_id = 1): array
    {
        $showroomList = $this->model
        ->select(['showrooms_id as showroom_id', 'image', 'title as showroom_title', 'address as showroom_address', 'google_map_link'])
        ->orderBy('showrooms_id')->findAll();
        
        foreach ($showroomList as $key => $showroom) {
            $image = !empty($showroom['image']) 
                ? json_decode($showroom['image'], true) 
                : [];
            $objectURL = $image[0]['objectURL'] ?? '';
            $showroomList[$key]['image'] = $objectURL;
        }

        if(!$showroomList || count($showroomList) == 0) {
            $showroomList = [];
        }
        $members = $this->getMemberByShowroomId($showroom_id);
        $salesTeams = $this->getSalesTeams();

        // $online = ['showroom_id' => 4, 'showroom_title' => 'Online', 'showroom_address' => null, 'google_map_link' => null];
        return [
            'members'   => $members ?? [],
            'locations' => $showroomList ?? [],
            'sales_teams' => $salesTeams ?? [] 
        ];
    }

    private function getSalesTeams(): array
    {
        $this->showroomContact->clearQuery();
        $salesTeams = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select([
            'showroom_contact.showroom_contact_id',
            'showroom_contact.image',
            'showroom_contact.name',
            'showroom_contact.email',
            'showroom_contact.phone',
            'showroom_contact.designation',
            'showroom_contact.message',
            'showrooms.title as showroom_title',
            'showrooms.showrooms_id as showroom_id'
        ])
        ->where('showroom_contact.status', '=', 1)
        ->where('showroom_contact.sales_team_contact', '=', 1);
        $salesTeams = $salesTeams->orderBy('showroom_contact.sort_order', 'asc')
        ->findAll();
        return $salesTeams;
    }

    private function getMemberByShowroomId(int $showroom_id): array
    {
        $this->showroomContact->clearQuery();
        $members = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select([
            'showroom_contact.showroom_contact_id',
            'showroom_contact.image',
            'showroom_contact.name',
            'showroom_contact.email',
            'showroom_contact.phone',
            'showroom_contact.designation',
            'showroom_contact.message',
            'showrooms.title as showroom_title',
            'showrooms.showrooms_id as showroom_id'
        ])
        ->whereIn('showroom_contact.designation', [
            'Internal Sales Representative',
            'Sales Executive',
            'Sales Representative'
        ])
        ->where('showroom_contact.status', '=', 1)
        ->where('showroom_contact.sales_team_contact', '=', 0);
        // if not 4
        if ($showroom_id != 4) {
            $members->where('showroom_contact.showroom_id', '=', $showroom_id);
        }
        $members = $members->orderBy('showroom_contact.sort_order', 'asc')
        ->findAll();

        foreach ($members as $key => $member) {
            $image = !empty($member['image']) 
                ? json_decode($member['image'], true) 
                : [];

            $objectURL = $image[0]['objectURL'] ?? '';
            $members[$key]['image'] = $objectURL;
        }
        return $members;
    }

    public function updateSlot(int $contact_id, array $data): array
    {
        $slot = $this->contactTimeSlot
            ->where('showroom_contact_id', '=', $contact_id)
            ->where('slot_time', '=', $data['value'])
            ->first();
    
        $slotData = [
            'showroom_contact_id' => $contact_id,
            'slot_time' => $data['value'],
            'note' => $data['note'] ?? null,
            'status' => $data['locked'] == 'true' ? 0 : 1,
        ];
    
        if (empty($slot)) {
            $slot = $this->contactTimeSlot->create($slotData);
        } else {
            $slot->update($slotData);
            // reload the model to get updated data
            // $slot->refresh();
        }
    
        return (array) $slot->data; // return array instead of stdClass
    }

    public function getSlot(int $contact_id): array
    {
        return $this->contactTimeSlot
            ->where('showroom_contact_id', '=', $contact_id)
            ->findAll();
    }
}
