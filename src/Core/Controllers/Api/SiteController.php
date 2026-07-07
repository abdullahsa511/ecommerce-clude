<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Components\Site;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Models\Site\SiteData;
use App\Core\Models\Site\SiteResponse;
use App\Core\Repositories\Site\SiteRepositoryInterface;

class SiteController extends ApiController
{
    private SiteRepositoryInterface $siteRepository;

    public function __construct(
        SiteRepositoryInterface $siteRepository,
    )
    {
        parent::__construct();
        $this->siteRepository = $siteRepository;
    }

    /**
     * Get all sites.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $sites = $this->siteRepository->findAll();
        return $this->renderResponse($sites);
    }

    /**
     * Show a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $site = $this->siteRepository->find((int)$id);
        if(!$site){
            return $this->renderError(404, 'Site not found');
        }
        $response = new SiteResponse($site->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new site.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $payload = $request->input('site');
            if (!is_array($payload) || !count($payload)) {
                throw new ValidationException([
                    'site' => ['The site payload must be a non-empty array.'],
                ]);
            }

            $sections = [
                'descriptionSettings',
                'localSettings',
                'mediaSettings',
                'commentSettings',
                'orderSettings',
                'seoSettings',
                'socialSettings',
                'siteSettings',
            ];
            
            $arrayData = [];
            
            // General settings (explicit)
            $generalSettingsKeys = [
                'name', 'key', 'host', 'theme', 'template',
                'admin_email', 'contact_email'
            ];
            
            foreach ($generalSettingsKeys as $key) {
                if (array_key_exists($key, $payload)) {
                    $arrayData[$key] = $payload[$key];
                }
            }
            
            // Merge all section settings dynamically
            foreach ($sections as $section) {
                if (!empty($payload[$section]) && is_array($payload[$section])) {
                    $arrayData = array_merge($arrayData, $payload[$section]);
                }
            }

             // Validate custom_date_format if it exists
            if (!empty($arrayData['custom_date_format'])) {
                $this->customValidateDate($arrayData['custom_date_format'], 20);
            }
             // Validate custom_time_format if it exists
            if (!empty($arrayData['custom_time_format'])) {
                $this->customValidateTime($arrayData['custom_time_format'], 20);
            }

            $request->validate($this->rules(), $arrayData);


            $siteData = new SiteData($payload);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $site = $this->siteRepository->createSite($siteData);
        if(!$site){
            return $this->renderError(500, 'Failed to create site');
        }
        $site = new SiteResponse($site->data);
        return $this->renderResponse($site);
    }
    
    // public function create_backup(Request $request): Response
    // {
    //     try {
    //         $payload = $request->input('site');
    //         if (!is_array($payload) || !count($payload)) {
    //             throw new ValidationException([
    //                 'site' => ['The site payload must be a non-empty array.'],
    //             ]);
    //         }

    //         try {
    //             $validated = $request->validate([
    //                 'name' => 'required|string',
    //                 'key' => 'required|string',
    //                 'host' => 'required|string',
    //                 'theme' => 'required|string',
    //                 'template' => 'required|string',
    //                 'admin_email' => 'required|email',
    //                 'contact_email' => 'required|email',
    //                 'descriptionSettings' => 'required|array',
    //                 'localSettings' => 'required|array',
    //                 'mediaSettings' => 'required|array',
    //                 'commentSettings' => 'required|array',
    //                 'orderSettings' => 'required|array',
    //                 'seoSettings' => 'required|array',
    //                 'socialSettings' => 'required|array',
    //                 'siteSettings' => 'required|array',
    //             ], $payload);
    //         } catch (ValidationException $e) {
    //             $validationErrors = $e->getErrors();
    //             $validated = array_intersect_key(
    //                 $payload,
    //                 array_flip([
    //                     'name',
    //                     'key',
    //                     'host',
    //                     'theme',
    //                     'template',
    //                     'admin_email',
    //                     'contact_email',
    //                     'descriptionSettings',
    //                     'localSettings',
    //                     'mediaSettings',
    //                     'commentSettings',
    //                     'orderSettings',
    //                     'seoSettings',
    //                     'socialSettings',
    //                     'siteSettings',
    //                 ])
    //             );
    //         }

    //         $descriptionSettings = [];
    //         $descriptionSettingsInput = $validated['descriptionSettings'] ?? ($payload['descriptionSettings'] ?? null);
    //         $localSettingsInput = $validated['localSettings'] ?? ($payload['localSettings'] ?? null);
    //         $mediaSettingsInput = $validated['mediaSettings'] ?? ($payload['mediaSettings'] ?? null);
    //         $commentSettingsInput = $validated['commentSettings'] ?? ($payload['commentSettings'] ?? null);
    //         $orderSettingsInput = $validated['orderSettings'] ?? ($payload['orderSettings'] ?? null);
    //         $seoSettingsInput = $validated['seoSettings'] ?? ($payload['seoSettings'] ?? null);
    //         $socialSettingsInput = $validated['socialSettings'] ?? ($payload['socialSettings'] ?? null);
    //         $siteSettingsInput = $validated['siteSettings'] ?? ($payload['siteSettings'] ?? null);

            

    //         if (!empty($validationErrors)) {
    //             throw new ValidationException($validationErrors);
    //         }

    //         $descriptionSettingsInput = is_array($descriptionSettingsInput) ? $descriptionSettingsInput : [];

            

    //         $payload = array_merge($payload, $validated);
    //         $payload['descriptionSettings'] = $descriptionSettingsInput;
    //         $payload['localSettings'] = $localSettingsInput;
    //         $payload['mediaSettings'] = $mediaSettingsInput;
    //         $payload['commentSettings'] = $commentSettingsInput;
    //         $payload['orderSettings'] = $orderSettingsInput;
    //         $payload['seoSettings'] = $seoSettingsInput;
    //         $payload['socialSettings'] = $socialSettingsInput;
    //         $payload['siteSettings'] = $siteSettingsInput;
    //         $siteData = new SiteData($payload);
    //     } catch (ValidationException $e) {
    //         return $this->renderError(422, $e->getMessage(), $e->getErrors());
    //     }

    //     $site = $this->siteRepository->createSite($siteData);
    //     if(!$site){
    //         return $this->renderError(500, 'Failed to create site');
    //     }
    //     $site = new SiteResponse($site->data);
    //     return $this->renderResponse($site);
    // }


   
    /**
     * Update a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */

    public function update(Request $request, int $id): Response
    {
        try {
            $payload = $request->input('site', []);
            if (!is_array($payload) || !count($payload)) {
                throw new ValidationException([
                    'site' => ['The site payload must be a non-empty array.'],
                ]);
            }


            $sections = [
                'descriptionSettings',
                'localSettings',
                'mediaSettings',
                'commentSettings',
                'orderSettings',
                'seoSettings',
                'socialSettings',
                'siteSettings',
            ];
            $arrayData = [];
            
            // General settings (explicit)
            $generalSettingsKeys = [
                'name', 'key', 'host', 'theme', 'template',
                'admin_email', 'contact_email'
            ];
            
            foreach ($generalSettingsKeys as $key) {
                if (array_key_exists($key, $payload)) {
                    $arrayData[$key] = $payload[$key];
                }
            }
            
            // Merge all section settings dynamically
            foreach ($sections as $section) {
                if (!empty($payload[$section]) && is_array($payload[$section])) {
                    $arrayData = array_merge($arrayData, $payload[$section]);
                }
            }

            if (!empty($arrayData['custom_date_format'])) {
                $this->customValidateDate($arrayData['custom_date_format'], 20);
            }
             // Validate custom_time_format if it exists
            if (!empty($arrayData['custom_time_format'])) {
                $this->customValidateTime($arrayData['custom_time_format'], 20);
            }

            $request->validate($this->rules(), $arrayData);

            $siteData = new SiteData(array_merge($payload));
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $site = $this->siteRepository->update($id, $siteData->toArray());
        if(!$site){
            return $this->renderError(500, 'Failed to update site');
        }
        $site = new SiteResponse($site->data);
        return $this->renderResponse($site);
    }


    // public function update_newBackup(Request $request, int $id): Response 
    // {
    //     try {
    //         $payload = $request->input('site', []);

    //         if (!is_array($payload) || !count($payload)) {
    //             throw new ValidationException([
    //                 'site' => ['The site payload must be a non-empty array.'],
    //             ]);
    //         }

    //         $validated = $request->validate([
    //             'site_id' => 'required|integer',
    //             'name' => 'required|string|max:255',
    //             'key' => 'required|string|max:255',
    //             'host' => 'required|string|max:255',
    //             'theme' => 'required|string|max:100',
    //             'template' => 'required|string|max:100',
    //             'admin_email' => 'required|email|max:255',
    //             'contact_email' => 'required|email|max:255',

    //             // Description settings
    //             'descriptionSettings' => 'nullable|array',
    //             'descriptionSettings.title' => 'nullable|string|max:255',
    //             'descriptionSettings.description' => 'nullable|string|max:1000',
    //             'descriptionSettings.meta_keywords' => 'nullable|string|max:500',
    //             'descriptionSettings.meta_description' => 'nullable|string|max:1000',
    //             'descriptionSettings.phone_number' => 'nullable|string|max:50',

    //             // Local settings
    //             'localSettings' => 'required|array',
    //             'localSettings.address' => 'required|string|max:500',
    //             'localSettings.geocode' => 'nullable|string|max:255',
    //             'localSettings.country_id' => 'nullable|integer',
    //             'localSettings.region_id' => 'nullable|integer',
    //             'localSettings.company' => 'nullable|string|max:255',
    //             'localSettings.vat_id' => 'nullable|string|max:100',
    //             'localSettings.language_id' => 'nullable|integer',
    //             'localSettings.currency_id' => 'nullable|integer',
    //             'localSettings.length_type_id' => 'nullable|integer',
    //             'localSettings.weight_type_id' => 'nullable|integer',
    //             'localSettings.timezone' => 'nullable|integer',
    //             'localSettings.date_format' => 'nullable|string|max:50',
    //             'localSettings.time_format' => 'nullable|string|max:50',
    //             'localSettings.custom_date_format' => 'nullable|string|max:50',
    //             'localSettings.custom_time_format' => 'nullable|string|max:50',

    //             // Media settings
    //             'mediaSettings' => 'nullable|array',
    //             'mediaSettings.*' => 'nullable|string|max:255', // all widths, heights, methods
    //             'mediaSettings.format' => 'nullable|string|max:50',
    //             'mediaSettings.quality' => 'nullable|integer',

    //             // Comment settings
    //             'commentSettings' => 'nullable|array',
    //             'commentSettings.allow_comments' => 'nullable|boolean',
    //             'commentSettings.logged_in_comments' => 'nullable|boolean',
    //             'commentSettings.break_comments' => 'nullable|boolean',
    //             'commentSettings.close_comments_days_old' => 'nullable|integer',
    //             'commentSettings.thread_comments_depth' => 'nullable|integer',
    //             'commentSettings.comments_per_page' => 'nullable|integer',
    //             'commentSettings.default_comments_page' => 'nullable|string|max:50',
    //             'commentSettings.comment_order' => 'nullable|in:older,newer',

    //             // Order settings
    //             'orderSettings' => 'nullable|array',
    //             'orderSettings.invoice_format' => 'nullable|string|max:100',
    //             'orderSettings.customer_order_format' => 'nullable|string|max:100',
    //             'orderSettings.display_weight' => 'nullable|boolean',
    //             'orderSettings.allow_guest_checkout' => 'nullable|boolean',
    //             'orderSettings.new_order_status' => 'nullable|string|max:50',
    //             'orderSettings.subtract_stock_status' => 'nullable|string|max:50',
    //             'orderSettings.enable_downloads_status' => 'nullable|string|max:50',

    //             // SEO settings
    //             'seoSettings' => 'nullable|array',
    //             'seoSettings.open_graph_title' => 'nullable|string|max:255',
    //             'seoSettings.open_graph_description' => 'nullable|string|max:1000',
    //             'seoSettings.twitter_title' => 'nullable|string|max:255',
    //             'seoSettings.twitter_description' => 'nullable|string|max:1000',
    //             'seoSettings.twitter_label_1' => 'nullable|string|max:100',
    //             'seoSettings.twitter_label_2' => 'nullable|string|max:100',
    //             'seoSettings.twitter_data_1' => 'nullable|string|max:255',
    //             'seoSettings.twitter_data_2' => 'nullable|string|max:255',

    //             // Social settings
    //             'socialSettings' => 'nullable|array',

    //             // Site settings (nested)
    //             'siteSettings' => 'nullable|array',
    //             'siteSettings.site_logo_settings' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo_dark' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo_sticky' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo_favicon' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.id' => 'nullable|integer',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.name' => 'nullable|string|max:255',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.size' => 'nullable|integer',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.type' => 'nullable|string|max:50',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.image' => 'nullable|string|max:500',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.status' => 'nullable|array',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.status.name' => 'nullable|string|max:50',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.status.severity' => 'nullable|string|max:50',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.media_id' => 'nullable|integer',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.objectURL' => 'nullable|url',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.created_at' => 'nullable|date',
    //             'siteSettings.site_logo_settings.site_logo_favicon.*.description' => 'nullable|string|max:500',
    //         ], $payload);

    //         $siteData = new SiteData(array_merge($payload, $validated));

    //     } catch (ValidationException $e) {
    //         return $this->renderError(422, $e->getMessage(), $e->getErrors());
    //     }

    //     $site = $this->siteRepository->update($id, $siteData->toArray());

    //     if (!$site) {
    //         return $this->renderError(500, 'Failed to update site');
    //     }

    //     $site = new SiteResponse($site->data);
    //     return $this->renderResponse($site);
    // }



    // public function update_backup(Request $request, int $id): Response
    // {
    //     try {
    //         $payload = $request->input('site', []);
    //         if (!is_array($payload) || !count($payload)) {
    //             throw new ValidationException([
    //                 'site' => ['The site payload must be a non-empty array.'],
    //             ]);
    //         }

    //         $validated = $request->validate([
    //             'name' => 'required|string',
    //             'key' => 'required|string',
    //             'host' => 'required|string',
    //             'theme' => 'required|string',
    //             'template' => 'required|string',
    //             'admin_email' => 'required|email',
    //             'contact_email' => 'required|email',
    //             'descriptionSettings' => 'nullable|array',
    //             'localSettings' => 'nullable|array',
    //             'mediaSettings' => 'nullable|array',
    //             'commentSettings' => 'nullable|array',
    //             'orderSettings' => 'nullable|array',
    //             'seoSettings' => 'nullable|array',
    //             'socialSettings' => 'nullable|array',
    //             'siteSettings' => 'nullable|array',
    //         ], $payload);

    //         $siteData = new SiteData(array_merge($payload, $validated));
    //     } catch (ValidationException $e) {
    //         return $this->renderError(422, $e->getMessage(), $e->getErrors());
    //     }

    //     $site = $this->siteRepository->update($id, $siteData->toArray());
    //     if(!$site){
    //         return $this->renderError(500, 'Failed to update site');
    //     }
    //     $site = new SiteResponse($site->data);
    //     return $this->renderResponse($site);
    // }

    // public function update(Request $request, int $id): Response
    // {
    //     try {
    //         $site = $request->input('site');
    //         $siteData = new SiteData($site);
    //     } catch (ValidationException $e) {
    //         return $this->renderError(422, $e->getMessage(), $e->getErrors());
    //     }

    //     $site = $this->siteRepository->update($id, $siteData->toArray());
    //     if(!$site){
    //         return $this->renderError(500, 'Failed to update site');
    //     }
    //     $site = new SiteResponse($site->data);
    //     return $this->renderResponse($site);
    // }

    /**
     * Delete a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        // $this->siteRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Site deleted successfully']);
    }



    // start create + update validation rules 
    private function rules(): array
    {
        $rules = [
            // Basic site info
            'name'          => 'required|string|max:255',
            'key'           => 'required|string|max:255',
            'host'          => 'required|string|max:255',
            'theme'         => 'required|string|max:255',
            'template'      => 'required|string|max:255',
            'admin_email'   => 'required|email',
            'contact_email' => 'required|email',
        
            // Description / SEO
            'title'            => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'meta_description' => 'nullable|string',
        
            // // Contact / Location
            'phone_number' => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'geocode'      => 'nullable|string',
            'country_id'   => 'nullable|integer',
            'region_id'    => 'nullable|integer',
        
            // // Company
            // 'company' => 'nullable|string|max:255',
            // 'vat_id'  => 'nullable|string|max:100', 
        
            // Localization
            'address'        => 'required|string|max:255',
            'company'        => 'required|string|max:255',
            'country_id'     => 'required|integer',
            'currency_id'    => 'required|integer',
            'date_format'    => 'nullable|string|max:50',
            'geocode'        => 'required|string|max:255',
            'language_id'    => 'nullable|integer',
            'length_type_id' => 'nullable|integer',
            'region_id'      => 'required|integer',
            'time_format'    => 'nullable|string|max:50',
            'timezone'       => 'nullable|string|max:100',
            'vat_id'         => 'required|string|max:100',
            'weight_type_id' => 'nullable|integer',

            // 'custom_date_format' => 'required|string|max:50',
            // 'custom_time_format' => 'required|string|max:50',
        
            // // Media
            'format'  => 'nullable|string',
            'quality' => 'nullable|string',
        
            // Image sizes (dynamic-friendly)
            'post_extra_large_width'  => 'required|integer',
            'post_extra_large_height' => '|integer',
            'post_extra_large_method' => 'nullable|string',
        
            'post_large_width'  => 'required|integer',
            'post_large_height' => 'nullable|integer',
            'post_large_method' => 'nullable|string',
        
            'post_medium_width'  => 'required|integer',
            'post_medium_height' => 'nullable|integer',
            'post_medium_method' => 'nullable|string',
        
            'post_thumb_width'  => 'required|integer',
            'post_thumb_height' => 'nullable|integer',
            'post_thumb_method' => 'nullable|string',
        
            'product_extra_large_width'  => 'nullable|integer',
            'product_extra_large_height' => 'nullable|integer',
            'product_extra_large_method' => 'nullable|string',
        
            'product_large_width'  => 'required|integer',
            'product_large_height' => 'nullable|integer',
            'product_large_method' => 'nullable|string',
        
            'product_medium_width'  => 'required|integer',
            'product_medium_height' => 'nullable|integer',
            'product_medium_method' => 'nullable|string',
        
            // // Comments
            // 'allow_comments'          => 'nullable|boolean',
            // 'logged_in_comments'      => 'nullable|boolean',
            // 'break_comments'          => 'nullable|boolean',
            // 'close_comments_days_old' => 'nullable|integer',
            // 'thread_comments_depth'   => 'nullable|integer',
            // 'comments_per_page'       => 'nullable|integer',
            // 'default_comments_page'   => 'nullable|string',
            // 'comment_order'           => 'nullable|string',
        
            // // Order
            // 'invoice_format'          => 'nullable|string',
            // 'customer_order_format'   => 'nullable|string',
            // 'display_weight'          => 'nullable|boolean',
            // 'allow_guest_checkout'    => 'nullable|boolean',
            // 'new_order_status'        => 'nullable|string',
            // 'subtract_stock_status'   => 'nullable|string',
            // 'enable_downloads_status' => 'nullable|string',
        
            // // Social
            // 'open_graph_title'        => 'nullable|string',
            // 'open_graph_description'  => 'nullable|string',
        
            // 'twitter_title'       => 'nullable|string',
            // 'twitter_description' => 'nullable|string',
            // 'twitter_label_1'      => 'nullable|string',
            // 'twitter_label_2'      => 'nullable|string',
            // 'twitter_data_1'       => 'nullable|string',
            // 'twitter_data_2'       => 'nullable|string',
        ];
        return $rules;
    }

    private function customValidateDate(string $format, int $maxLength): void
    {
        if (strlen($format) > $maxLength) {
            throw new ValidationException([
                'custom_date_format' => ["The date format must not exceed {$maxLength} characters."],
            ]);
        }
    
        // 2. Allowed formats (STRICT)
        $allowedFormats = [
            'YYYY/MM/DD',
            'MM/YYYY/DD',
            'DD MM',
            'YYYY',
            'YYYY-MM-DD',
            'HH:mm:ss',
            'YYYY-MM-DD HH:mm:ss',
        ];
    
        // 3. Exact match check
        if (!in_array($format, $allowedFormats, true)) {
            throw new ValidationException([
                'custom_date_format' => ['The date format is not allowed.'],
            ]);
        }
    }   

    private function customValidateTime(string $time, int $maxLength): void
    {
        // 1. Length check
        if (strlen($time) > $maxLength) {
            throw new ValidationException([
                'custom_time_format' => ["The custom time format must not exceed {$maxLength} characters."],
            ]);
        }

        // 2. Allowed time formats (STRICT)
        $allowedFormats = [
            'HH:mm',        // 24h
            'HH:mm:ss',     // 24h with seconds
            'H:mm',         // 24h (no leading zero)
            'H:mm:ss',      // 24h with seconds (no leading zero)

            'hh:mm A',      // 12h
            'hh:mm:ss A',   // 12h with seconds
            'h:mm A',       // 12h (no leading zero)
            'h:mm:ss A',    // 12h with seconds (no leading zero)

            'H:i:s A',      
            'h:i:s A',      
            'H:i a',      
            'h:i A',      
            'H:i',      
            'h:i',      
            'H:i:s',      
            'h:i:s',    

            'G:i:s',      
            'g:i:s',      
            'G:i',      
            'g:i',      
            'G:i:s A',      
            'g:i:s A',      
            'G:i A',      
            'g:i A',      

        ];

        // 3. Exact match validation
        if (!in_array($time, $allowedFormats, true)) {
            throw new ValidationException([
                'custom_time_format' => ['The time format is not allowed.'],
            ]);
        }
    }



    // private function customValidateDate(string $date, int $maxLength)
    // {
    //     if (strlen($date) > $maxLength) {
    //         throw new ValidationException([
    //             'custom_date_format' => ["The custom date format must not exceed $maxLength characters."],
    //         ]);
    //     }
    
    //     $formats = [
    //         'Y/m/d',               // YYYY/MM/DD          -> 2024/01/01
    //         'Y-m-d',               // YYYY-MM-DD          -> 2024-01-01
    //         'm/d/Y',               // MM/DD/YYYY          -> 01/01/2024
    //         'd-m-Y',               // DD-MM-YYYY          -> 01-01-2024
    //         'H:i:s',               // HH:mm:ss            -> 12:00:00 (24h)
    //         'h:i:s A',             // hh:mm:ss AM/PM      -> 12:00:00 PM (12h)
    //         'd M Y',               // DD MMM YYYY         -> 01 Jan 2024
    //         'd F Y',               // DD MMMM YYYY        -> 01 January 2024
    //         'Ymd',                 // YYYYMMDD            -> 20240101
    //         'Y-m-d H:i:s',         // YYYY-MM-DD HH:mm:ss -> 2024-01-01 12:00:00
    //         'Y-m-d\TH:i:sP',       // YYYY-MM-DDTHH:mm:ssZ (ISO-like) -> 2024-01-01T12:00:00+00:00
    //     ];
    

    //     $dateObj = null;

    //     foreach ($formats as $format) {
    //         $temp = \DateTime::createFromFormat($format, $date);
    //         if ($temp && $temp->format($format) === $date) {
    //             $dateObj = $temp;
    //             break;
    //         }
    //     }

    //     if (!$dateObj) {
    //         throw new ValidationException([
    //             'custom_date_format' => ['The custom date format must be a valid date.'],
    //         ]);
    //     }
    // }

    // private function customValidateTime(string $time, int $maxLength)
    // {
    //     if (strlen($time) > $maxLength) {
    //         throw new ValidationException([
    //             'custom_time_format' => ["The custom time format must not exceed $maxLength characters."],
    //         ]);
    //     }
    //     $formats = [
    //         'H:i a',      // 24h with seconds
    //         'H:i:s',      // 24h with seconds
    //         'H:i',        // 24h without seconds
    //         'h:i:s A',    // 12h with seconds
    //         'h:i A',      // 12h without seconds
    //         'G:i:s',      // 24h without leading zero
    //         'g:i A',      // 12h without leading zero
    //     ];
    //     $timeObj = null;
    //     foreach ($formats as $format) {
    //         $temp = \DateTime::createFromFormat($format, $time);
    //         if ($temp && $temp->format($format) === $time) {
    //             $timeObj = $temp;
    //             break;
    //         }
    //     }
    //     if (!$timeObj) {
    //         throw new ValidationException([
    //             'custom_time_format' => ['The custom time format must be a valid time.'],
    //         ]);
    //     }
    // }
    // end create + update validation rules 

    
   
   
} 