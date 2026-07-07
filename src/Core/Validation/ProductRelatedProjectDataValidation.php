<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductRelatedProjectDataValidation extends Validation
{
    public stdClass $productRelatedProject;

    public function __construct(
        array $data, 
        array $requiredFields = [], 
        array $textFields = [], 
        array $existingProductRelatedProjectsMap = [],
        array $projectSlugMap = [],
        array $productCodeMap = []
    ){
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productRelatedProject = new stdClass();

        $projectSlug = strtolower(str_replace(' ', '-', $data['project_slug']));
        $productCode = strtolower(str_replace(' ', '-', $data['product_code']));

        $projectId = $projectSlugMap[$projectSlug] ?? null;
        if(!isset($projectId)){
            $this->addError('project_slug', 'Project not found');
            return;
        }

        $productId = $productCodeMap[$productCode] ?? null;
        if(!isset($productId)){
            $this->addError('product_code', 'Product not found');
            return;
        }

        if(isset($projectId) && isset($productId)){
            $this->productRelatedProject->project_id = $projectId;
            $this->productRelatedProject->product_id = $productId;

            if(isset($existingProductRelatedProjectsMap[$projectId . '-' . $productId])){
                $this->isExistingData = true;
            }
        }

        $this->productRelatedProject->sort_order = $this->validateInteger($data['sort_order'], 'sort_order', 0);
    }

    public function toArray(): array
    {
        return (array) $this->productRelatedProject;
    }
}