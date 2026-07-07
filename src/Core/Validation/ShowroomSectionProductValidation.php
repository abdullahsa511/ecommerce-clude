<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ShowroomSectionProductValidation extends Validation
{
    public stdClass $sectionProduct;
    public function __construct(
        array $data,
        array $requiredFields = [],
        array $textFields = [],
        $maps = ['productIdsMap' => [], 'sectionIdsMap' => []],
        array $existingDataMap = []
    ) {
        parent::__construct($requiredFields, $textFields);
        $this->sectionProduct = new stdClass();
        $this->rawData = $data;
        $productCode = $this->validateString($data['product_code'], 'product_code', 191, true);
        $sectionCode = $this->validateString($data['section_code'], 'section_code', 191, true);
        $productId = $maps['productIdsMap'][$productCode] ?? null;
        $sectionId = $maps['sectionIdsMap'][$sectionCode.'-'.$data['showroom_id']] ?? null;
        $combindedValue = $sectionId . "-" . $productId;

        if (!isset($productId)) {
            $this->addError('product_id', 'not found');
        }
        if (!isset($sectionId)) {
            $this->addError('section_id', 'not found');
        }
        // check if the combinded value is in the existing data map
        $existingDataValues = array_values($existingDataMap);
        if (isset($existingDataValues[$combindedValue])) {
            $this->isExistingData = true;
            $this->sectionProduct->project_section_products_id = $existingDataValues[$combindedValue];
        }else{
            $this->isExistingData = false;
        }

        $this->sectionProduct->section_id = $this->validateInteger($sectionId, 'section_id');
        $this->sectionProduct->product_id = $this->validateInteger($productId, 'product_id');
        if (isset($data['sort_order'])) $this->sectionProduct->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0);
        if (isset($data['status'])) $this->sectionProduct->status = json_encode(['active' => true]);
        if(isset($data['finish_material'])) $this->sectionProduct->finish_material = $this->validateString($data['finish_material'], 'finish_material', 1000) ?? '';
        // if (isset($data['project_section_products_id'])) {
        //     $this->isExistingData = !!$existingDataMap[$data['project_section_products_id']] ?? false;
        //     $this->sectionProduct->project_section_products_id = $this->validateInteger($data['project_section_products_id'], 'project_section_products_id');
        // }
    }
    public function toArray(): array
    {
        return (array)$this->sectionProduct;
    }
}
