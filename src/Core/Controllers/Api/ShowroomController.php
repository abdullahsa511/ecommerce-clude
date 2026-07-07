<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\Models\Showroom\ProjectSectionProduct;
use App\Core\Models\Showroom\ProjectSectionImage;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use Exception;
use Illuminate\Container\Attributes\Log;
use App\Core\Validation\FieldValidator;
// use App\Traits\FileUploadTrait;

class ShowroomController extends ApiController
{
    // use FileUploadTrait;
    private ShowroomRepositoryInterface $showroomRepository;
    private ProjectSectionProduct $ProjectSectionProduct;
    private ProjectSectionImage $ProjectSectionImage;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        ShowroomRepositoryInterface $showroomRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        // echo "55". PHP_EOL; exit;
        parent::__construct();
        $this->showroomRepository = $showroomRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all orders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // api data structure for showroom
        $showrooms = $this->showroomRepository->findAll();
        return $this->renderResponse($showrooms);
    }
    public function getSections(Request $request, $slug): Response
    {
        $showrooms = $this->dbData();
        // Return a specific showroom if slug exists, else all
        if (isset($showrooms[$slug])) {
            return $this->renderResponse($showrooms[$slug]);
        }

        return $this->renderResponse($showrooms);
    }

    // real data fetch from db
    public function sectonDetails(Request $request, $showroom_slug, $slug): Response
    {
        $showroom = $this->showroomRepository->sectionDetails($showroom_slug, $slug);
        if (!$showroom) {
            return $this->renderError(404, 'Section not found');
        }
        $showroom['image'] = is_string($showroom['image']) ? json_decode($showroom['image'], true) : $showroom['image'];
        return $this->renderResponse($showroom);
    }
    public function sectonDetailById(Request $request, $id): Response
    {
        // Get the section (as object)
        $showroom = $this->showroomRepository->findSectionById($id);

        // Decode image JSON if it's a string
        if (isset($showroom->image) && is_string($showroom->image)) {
            $showroom->image = json_decode($showroom->image, true);
        }

        return $this->renderResponse($showroom);
    }

    private function dbData()
    {
        $data = [
            'collaborative-hub' => [
                'id' => 1,
                'name' => 'Collaborative Hub',
                'slug' => 'collaborative-hub',
                'description' => 'A dynamic space designed to foster teamwork and innovation. Features flexible seating arrangements and interactive technology displays.',
                'image' => '/img/showroom/collaborative-hub.png',
                'features' => [
                    'Flexible seating configurations',
                    'Interactive digital displays',
                    'Meeting pods for private discussions',
                    'Whiteboard walls for brainstorming',
                    'Wireless charging stations',
                ],
                'products' => [
                    ['id' => 1, 'name' => 'Collaborative Table ', 'price' => 2500],
                    ['id' => 2, 'name' => 'Meeting Pod', 'price' => 3500],
                ],
            ],
            'work-hub' => [
                'id' => 2,
                'name' => 'Work Hub',
                'slug' => 'work-hub',
                'description' => 'A dedicated workspace designed for focused productivity and individual work.',
                'image' => '/img/showroom/work-hub.png',
                'features' => [
                    'Ergonomic workstations',
                    'Privacy screens',
                    'Adjustable height desks',
                    'Task lighting',
                    'Cable management systems',
                ],
                'products' => [
                    ['id' => 3, 'name' => 'Standing Desk', 'price' => 1200],
                    ['id' => 4, 'name' => 'Ergonomic Chair', 'price' => 800],
                ],
            ],
            'adaptive-solutions' => [
                'id' => 3,
                'name' => 'Adaptive Solutions',
                'slug' => 'adaptive-solutions',
                'description' => 'A premium space designed for executives, featuring luxury furniture and sophisticated technology.',
                'image' => '/img/showroom/executive-suite.png',
                'features' => [
                    'Premium leather seating',
                    'Conference area with smart board',
                    'Ambient lighting controls',
                    'Integrated AV systems',
                    'Acoustic wall panels',
                ],
                'products' => [
                    ['id' => 5, 'name' => 'Executive Desk', 'price' => 4500],
                    ['id' => 6, 'name' => 'Chair Display Board', 'price' => 5200],
                ],
            ],
            'chair-display' => [
                'id' => 3,
                'name' => 'Chair Display',
                'slug' => 'chair-display',
                'description' => 'A premium space designed for executives, featuring luxury furniture and sophisticated technology.',
                'image' => '/img/showroom/executive-suite.png',
                'features' => [
                    'Premium leather seating',
                    'Conference area with smart board',
                    'Ambient lighting controls',
                    'Integrated AV systems',
                    'Acoustic wall panels',
                ],
                'products' => [
                    ['id' => 5, 'name' => 'Executive Desk', 'price' => 4500],
                    ['id' => 6, 'name' => 'Smart Board', 'price' => 5200],
                ],
            ],
            'conference' => [
                'id' => 3,
                'name' => 'Conference',
                'slug' => 'conference',
                'description' => 'A premium space designed for executives, featuring luxury furniture and sophisticated technology.',
                'image' => '/img/showroom/executive-suite.png',
                'features' => [
                    'conference leather seating',
                    'Conference area with smart board',
                    'Ambient lighting controls',
                    'conference AV systems',
                    'Acoustic wall panels',
                ],
                'products' => [
                    ['id' => 5, 'name' => 'Conference Desk', 'price' => 4500],
                    ['id' => 6, 'name' => 'Conference Board', 'price' => 5200],
                ],
            ],
        ];
        return $data;
    }

    /**
     * 
     * 
     */

    /**
     * List Showrooms
     * use getShowroom rep method
     * @param option = [] defult are null.
     */
    public function list(): Response
    {
        $showrooms = $this->showroomRepository->getShowroom();
        if (!$showrooms) {
            return $this->renderError(404, 'Showrooms not found');
        }
        return $this->renderResponse($showrooms);
    }

    /**
     * view showroom
     * use getShowroom rep method
     * @param id param .
     */
    public function show(Request $request, $id)
    {
        if (!$id) {
            return $this->renderError(400, 'ID is required');
        }

        $showroom = $this->showroomRepository->findById($id);

        if (!$showroom) {
            return $this->renderError(404, 'Showroom not found');
        }

        // Convert object to array
        if (is_object($showroom)) {
            $showroom = method_exists($showroom, 'toArray') ? $showroom->toArray() : (array) $showroom;
        }
        $imageProperties = ['image', 'banner_image', 'overview_image'];
        foreach ($imageProperties as $property) {
            if (isset($showroom[$property]) && is_string($showroom[$property])) {
                $image = json_decode($showroom[$property], true);
                foreach ($image as $key => $value) {
                    $image[$key]['status'] = ['name' => 'Uploaded', 'severity' => 'success'];
                }
                $showroom[$property] = $image;
            }
        }

        return $this->renderResponse($showroom);
    }

    public function store(Request $request): Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string',
                'slug' => 'required|string',
                'description' => 'required|string',
                'address' => 'nullable|string',
                'email' => 'nullable|email',
                'phone' => 'nullable|phone',
                'opening_hours' => 'nullable|string',
                'google_map_link' => 'nullable|string',
                'status' => 'nullable|string',
                'is_section_active' => 'nullable|integer',
            ]);

            if ($data instanceof Response) {
                return $data;
            }
            $showroom = $this->showroomRepository->createShowroom($data);
            return $this->renderResponse($showroom);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function update(Request $request, int $id): Response
    {
        try {
            // Fetch existing record
            $existingShowroom = $this->showroomRepository->findById($id);

            if (!$existingShowroom) {
                return $this->renderError(404, 'Showroom not found');
            }

            $existingShowroom = (array) $existingShowroom;
            
            $data = $request->validate([
                'title' => 'nullable|string',
                'slug' => 'nullable|string',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'email' => 'nullable|email',
                'phone' => 'required|phone',
                'opening_hours' => 'nullable|string',
                'google_map_link' => 'nullable|string',
                'status' => 'nullable|string',
                'is_section_active' => 'nullable|integer',
                // 'image' => 'nullable',
            ]);

            if ($data instanceof Response) {
                return $data;
            }

            $updatedShowroom = $this->showroomRepository->updateShowroom($id, $data);
            
            if (!$updatedShowroom) {
                return $this->renderError(402, 'Failed to update showroom');
            }

            return $this->renderResponse($updatedShowroom);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, 'what happen error. ' .  $e->getMessage());
        }
    }

    public function delete(Request $request, $id): Response
    {
        $showrooms = $this->showroomRepository->deleteShowroom((int) $id);
        return $this->renderResponse($showrooms);
    }

    public function showroomSectionProductImage(): Response
    {
        $showrooms = $this->showroomRepository->showroomDetails();
        if (!$showrooms) {
            return $this->renderError(404, 'Showrooms not found');
        }
        return $this->renderResponse($showrooms);
    }

    public function showroomSectionLists(Request $request, $id): Response
    {
        // Debug::dd($request);
        // api data structure for showroom
        $showrooms = $this->showroomRepository->findShowroomSectionById($id);
        // return $showrooms;
        return $this->renderResponse($showrooms);
    }

    public function addSection(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'section_code' => 'required|string',
                'title'        => 'required|string',
                'slug'         => 'required|string',
                'description'  => 'required|string',
                'status'       => 'nullable|string',
                'sort_order'   => 'nullable|numeric',
            ]);

            $showroom = $this->showroomRepository->addSection($id, $data);

            // Handle duplicate section code
            if (isset($showroom['duplicate']) && $showroom['duplicate'] === true) {
                return $this->renderError(500, 'Try again. Duplicate section code.');
            }

            // Handle if section could not be created
            if (empty($showroom)) {
                return $this->renderError(500, 'Failed to create section. Please try again.');
            }

            // Success
            return $this->renderResponse($showroom);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function updateSection(Request $request, int $id): Response
    {
        try {
            // Validate input
            $data = $request->validate([
                'title' => 'nullable|string',
                'section_code' => 'nullable|string',
                'slug' => 'nullable|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'sort_order' => 'nullable|number',
            ]);

            if ($data instanceof Response) {
                return $data;
            }

            // Save data in repository
            $section = $this->showroomRepository->updateSection($id, $data);

            return $this->renderResponse($section);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    public function deleteSection(Request $request, int $id): Response
    {
        $showrooms = $this->showroomRepository->deleteSection($id);
        return $this->renderResponse($showrooms);
    }


    public function sectionImages(Request $request, int $id): Response
    {
        $sectionImages = $this->showroomRepository->sectionImages($id);
        return $this->renderResponse($sectionImages);
    }
    public function addSectionImage(Request $request, int $id, int $showroom_id): Response
    {
        $sectionImages = $this->uploadShowroomImage('section-gallery', $request->files(), $id, $showroom_id);
        if (isset($sectionImages['error']) && count($sectionImages['error'])) {
            return $this->renderError(422, json_encode($sectionImages['error']));
        }
        $sectionImagesToAdd = [];
        foreach ($sectionImages['files'] as $key => $value) {
            $sectionImagesToAdd[] = [
                'section_id' => $id,
                'image_link' => $value['objectURL'] ?? '',
                'image' => json_encode([$value]),
                'status' => isset($value['status']) ? json_encode($value['status']) : '{"active": true}',
                'sort_order' => $key,
            ];
        }
        if (count($sectionImagesToAdd)) {
            $this->showroomRepository->addSectionImages($sectionImagesToAdd);
        }
        $sectionImages = $this->showroomRepository->sectionImages($id);
        return $this->renderResponse($sectionImages);
    }
    public function updateSectionImage(Request $request, int $id): Response
    {
        $data = [];
        $showrooms = $this->showroomRepository->updateSectionImage($id, $data);
        return $this->renderResponse($showrooms);
    }
    public function sectionProducts(Request $request, int $id): Response
    {
        $showrooms = $this->showroomRepository->sectionProducts($id);
        return $this->renderResponse($showrooms);
    }
    public function addSectionProduct(Request $request, int $id): Response
    {
        $productId = $request->input('product_id');
        if (!$productId) {
            return $this->renderError(422, 'Product ID is required');
        }
        $showrooms = $this->showroomRepository->addSectionProduct($id, $productId);
        return $this->renderResponse($showrooms);
    }

    public function deleteSectionProductById(Request $request, int $id, int $project_section_products_id): Response
    {
        $showrooms = $this->showroomRepository->deleteSectionProduct($id, $project_section_products_id);
        return $this->renderResponse($showrooms);
    }
    public function deleteSectionImage(Request $request, int $id, int $imageId): Response
    {
        $showrooms = $this->showroomRepository->deleteSectionImage($id, $imageId);
        return $this->renderResponse($showrooms);
    }

    /**
     * Uploads a single image using raw PHP and returns a JSON string like:
     * [
     *   {
     *     "alt": "Title",
     *     "objectURL": "/img/showroom/filename.png"
     *   }
     * ]
     *
     */
    public function uploadFile(string $fieldName = 'image', ?string $alt = null): string
    {
        // Check if file is uploaded
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return json_encode([]);
        }

        $fileInfo = $_FILES[$fieldName];

        $fileTmpPath = $fileInfo['tmp_name'];
        $originalName = $fileInfo['name'];
        $fileType = $fileInfo['type'];
        $fileSize = $fileInfo['size'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Optional: Validate allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowedExtensions)) {
            return json_encode([]);
        }

        // Sanitize filename
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $newFileName = $safeName . '.' . $extension;

        // Upload directory
        $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/img/showrooms/';

        // file folder permission for upload
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $newFileName;

        // Move uploaded file
        if (!is_uploaded_file($fileTmpPath) || !move_uploaded_file($fileTmpPath, $destPath)) {
            return json_encode([]);
        }

        // Build JSON structure with extra metadata
        $imageData = [
            [
                'alt' => $alt ?? $safeName,
                'objectURL' => '/img/showroom/' . $newFileName,
                'full_path' => $originalName,      // original file name as provided by client
                'type' => $fileType,          // MIME type, e.g. image/png
                'tmp_name' => $fileTmpPath,       // temporary path (for logging/debug)
                'size' => $fileSize,          // size in bytes
                'extension' => $extension,         // file extension
                'new_name' => $newFileName,       // stored filename
                'absolute_path' => $destPath        // actual server path (useful for future deletes)
            ]
        ];

        return json_encode($imageData);
    }

    public function upload(Request $request, string $context, int $id, int $showroom_id): Response|array
    {
        // var_dump($request->files());
        if ($request->files() || isset($_FILES['files'])) {
            //Double check when request will send from vue application.
            $f = $request->files() ?? $_FILES['files'];
            $result = $this->uploadShowroomImage($context, $f, $id, $showroom_id);
            if ($result instanceof Response) {
                return $result;
            }
            if (!$result) {
                return $this->renderError(402, 'Failed to upload media');
            }
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    private function uploadShowroomImage($context, $files, $id, $showroom_id)
    {
        if (!in_array($context, ['showroom', 'section', 'section-gallery', 'banner_image', 'overview_image'])) {
            return false;
        }


        $path = match ((int) $showroom_id) {
            1 => '/sydney',
            2 => '/melbourne',
            3 => '/brisbane',
            default => '',
        };

        $property = 'image';
        $size = [
            'width' => 883,
        'height' => 664
        ];
        $folder = 'main';
        $is_banner = false;
        // Override size based on property
        if ($context === 'section') {
            $size = [
                'width' => 316,
                'height' => 242,
            ];
            $folder = 'sections' .$path;
        } elseif ($context === 'section-gallery') {
            $size = [
                'width' => null,
                'height' => null,
            ];
            $folder = 'gallery' .$path;
        } elseif ($context === 'banner_image') {
            $size = [
                'width' => 1600,
                'height' => 688,
            ];
            $folder = 'banner-image';
            $property = 'banner_image';
            $is_banner = true;
        } elseif($context === 'overview_image'){
            $size = [
                'width' => 1150,
                'height' => 630,
            ];
            $folder = 'overview-image';
            $property = 'overview_image';
        }

        if ($files && count($files)) {

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data['files'] = $files;
            $data['upload_dir'] = 'media/showrooms/' . $folder;

            $result = $this->mediaRepository->upload($data, $size, 'media/showrooms/' . $folder, null, $is_banner);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
            if (isset($result['files']) && is_array($result['files']) && count($result['files']) > 0) {
                if ($context === 'section') {
                    $section = $this->showroomRepository->updateSection($id, ['files' => $result['files']]);
                    if (!$section) {
                        return false;
                    }
                    return $section;
                } elseif ($context === 'section-gallery') {
                    return $result;
                } else {
                    //Showroom Image
                    $showroom = $this->showroomRepository->updateShowroom($id, ['files' => $result['files']], $property);
                    if (!$showroom) {
                        return false;
                    }
                    return $showroom;
                }
            }

            if (isset($result['error']) && is_array($result['error']) && count($result['error']) > 0) {
                return $this->renderError(422, 'Upload validation failed', $result['error']);
            }

            return $this->renderError(422, 'No files were stored from this upload');
        }
    }
    public function deleteImageByProperty(Request $request, int $showroom_id, string $property): Response
    {
        $type = $request->query('type');
        $deleted = $this->showroomRepository->deleteImageByProperty($showroom_id, $property, $type);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'success' => $deleted]);
    }

    public function importSections(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->showroomRepository->importSections($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importSectionsImages(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->showroomRepository->importSectionsImages($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importSectionsProducts(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->showroomRepository->importSectionProducts($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function getShowroomForPinboard()
    {
        try {
            $showroom = $this->showroomRepository->getShowroomForPinboard();
            return $this->renderResponse($showroom);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to get showroom for pinboard: ' . $e->getMessage());
        }
    }

    public function updateWayPoints(Request $request): Response
    {
        $data = $request->all();
        $this->showroomRepository->updateWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }

    // start showroom contact
    public function getShowroomContactList(): Response
    {
        $showroomContactList = $this->showroomRepository->getShowroomContactList();
        return $this->renderResponse($showroomContactList);
    }
    public function getShowroomContactById(Request $request, int $showroom_contact_id): Response
    {
        $showroomContact = $this->showroomRepository->getShowroomContactById($showroom_contact_id);
        return $this->renderResponse($showroomContact);
    }
    public function deleteShowroomContactById(Request $request, int $showroom_contact_id): Response
    {
        $showroomContact = $this->showroomRepository->deleteShowroomContactById($showroom_contact_id);
        return $this->renderResponse($showroomContact);
    }
    public function createShowroomContact(Request $request): Response
    {
        $data = $request->all();
        // validate data
        $request->validate([
            'showroom_id' => 'required|integer',
            'name' => 'required|string'
        ]);
        $showroomContact = $this->showroomRepository->createShowroomContact($data);
        return $this->renderResponse($showroomContact);
    }
    public function updateShowroomContactById(Request $request, int $showroom_contact_id): Response
    {
        $data = $request->all();
        $showroomContact = $this->showroomRepository->updateShowroomContactById($showroom_contact_id, $data);
        return $this->renderResponse($showroomContact);
    }
    public function importShowroomContact(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        $showroomContact = $this->showroomRepository->importShowroomContact($csv_file_path);
        return $this->renderResponse($showroomContact);
    }

    // upload showroom contact image
    // public function uploadShowroomContactImage(Request $request, int $showroom_contact_id): Response|array
    // {
    //     // $data = $request->all();
    //     $folder = 'media/Showroom-contacts/';
    //     $size = [
    //         'width' => 420,
    //         'height' => 420,
    //     ];
    //     $result = null;
    //     if ($request->files() || isset($_FILES['files'])) {
    //         $files = $request->files() ?? $_FILES['files'];

    //         if (!count($files)) {
    //             return $this->renderError(422, 'No files uploaded');
    //         }
    //         $uploadData = [
    //             'files' => $files,
    //             'upload_dir' => $request->input('upload_dir', $folder)
    //         ];

    //         $result = $this->mediaRepository->upload($uploadData, $size, $folder);
    //         if (!$result) {
    //             return $this->renderError(500, 'Failed to upload media');
    //         }
           
    //     }
    //     $showroomContact = $this->showroomRepository->updateShowroomContact($showroom_contact_id, isset($result['files']) ? $result['files'] : []);
    //     return $this->renderResponse($showroomContact);
    // }

    public function uploadShowroomContactImage(Request $request, int $showroom_contact_id): Response
    {
        // Set default size
        $size = [
            'width' => 900,
            'height' => 900,
        ];

        $folder = 'media/Showroom-contacts/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->showroomRepository->updateShowroomContactImage($showroom_contact_id, $result['files']);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete showroom contact image
    public function deleteShowroomContactImage(Request $request, int $showroom_contact_id): Response
    {
        $deleted = $this->showroomRepository->deleteShowroomContactImage($showroom_contact_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
    
    // end showroom contact
    public function updateSlot(Request $request, int $showroom_contact_id): Response
    {
        $data = $request->all();
        $slot = $this->showroomRepository->updateSlot($showroom_contact_id, $data);
        if (!$slot) {
            return $this->renderError(500, 'Failed to update slot');
        }
        return $this->renderResponse(['success' => $slot]);
    }
    public function getSlot(Request $request, int $showroom_contact_id): Response
    {
        $slots = $this->showroomRepository->getSlot($showroom_contact_id);
        return $this->renderResponse($slots);
    }
    public function getMembersByShowroomId(Request $request, int $showroom_id): Response
    {
        $members = $this->showroomRepository->getMembersData($showroom_id);
        return $this->renderResponse($members);
    }
}
