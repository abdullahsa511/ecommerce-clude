<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ShowroomSectionImageValidation extends Validation
{
    public stdClass $sectionImage;
    public stdClass $media;
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $sectionImageMaps = [], array $sectionIds = [])
    {
        parent::__construct($requiredFields, $textFields);
        $this->sectionImage = new stdClass();
        $this->rawData = $data;

        $showroom_id = isset($data['showroom_id']) ? $data['showroom_id'] : 1;
        $imagePath = match ((int) $showroom_id) {
            1 => '/media/showrooms/gallery/sydney',
            2 => '/media/showrooms/gallery/melbourne',
            3 => '/media/showrooms/gallery/brisbane',
            default => '/media/showrooms/gallery/sydney',
        };

        if(!isset($data['section_id'])){
            $this->addError('section_id', 'is mandatory');
            return;
        }

        $sectionImages = isset($sectionImageMaps[$data['section_id']]) ? $sectionImageMaps[$data['section_id']] : [];
        $sectionImageIds = array_values($sectionImages);
        // if (in_array($sectionImageIds, $existingDataValues)) {
        //     $this->isExistingData = true;
        //     $this->sectionImage->project_section_images_id = $existingDataValues[$sectionImageIds];
        // }
        
        // SECTION ID VALIDATION
        $sectionCode = $this->validateString($data['section_code'], 'section_code', 191, true);
        $uniqueSectionCode = $sectionCode.'-'.$showroom_id;
        $sectionId = isset($data['section_id']) ? $this->validateInteger($data['section_id'], 'section_id') : (isset($sectionIds[$uniqueSectionCode]) ? $sectionIds[$uniqueSectionCode] : null);

        if(!isset($sectionId) || empty($sectionId)){
            $this->addError('section_code', 'Section code not found');
            // return;
        }else{
            $this->sectionImage->section_id = $sectionId;
        }

        if(!isset($data['image_link']) || empty($data['image_link'])){
            $this->addError('image_link', 'Image link is required');
        }else{
           $this->sectionImage->image_link = $this->validateString($data['image_link'], 'image_link', 191);
           $this->sectionImage->image = $this->validateJson($data['image_link'], 'image', $imagePath);
        }
        
        // Initialize with proper type casting and validation
        if(isset($data['project_section_images_id'])) $this->sectionImage->project_section_images_id = $this->validateInteger($data['project_section_images_id'], 'project_section_images_id');
        // if(isset($data['section_id'])) $this->sectionImage->section_id = $this->validateInteger($data['section_id'], 'section_id');
        if(isset($data['sort_order'])) $this->sectionImage->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0);
        if(isset($data['sort_order'])) $this->sectionImage->status = $this->validateInteger($data['sort_order'], 'sort_order', 0);
        if(isset($this->sectionImage->project_section_images_id)){
            $this->isExistingData = isset($sectionImageIds[$this->sectionImage->project_section_images_id]);
        }

        if(isset($this->sectionImage->image_link) && isset($sectionImages[$this->sectionImage->image_link])){
            $this->isExistingData = true;
        }

        // media
        if(isset($this->sectionImage->image) && !empty($this->sectionImage->image)){
            $image = json_decode($this->sectionImage->image, true);
            $image = $image[0]??[];
            if(isset($image) && !empty($image['objectURL']) && isset($image['file'])){
                $this->media = new stdClass();
                $this->media->file = json_encode([
                    'name' => $image['name'],
                    'size' => $image['size'],
                    'type' => $image['type'],
                    'objectURL' => $image['objectURL'],
                    'tmp_name' => $image['file']['tmp_name'],
                    'full_path' => $image['file']['full_path'],
                ]);
                $this->media->path = $image['objectURL'];
                $this->media->name = $image['name'];
                $this->media->meta = $this->sectionImage->image_link;
            }
        }
    }
    public function toArray(): array
    {
        return (array)$this->sectionImage;
    }
}
