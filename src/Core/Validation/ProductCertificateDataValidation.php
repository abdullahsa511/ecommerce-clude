<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductCertificateDataValidation extends Validation
{
    public stdClass $productCertificate;

    public function __construct(
        array $data, 
        array $requiredFields = [], 
        array $textFields = [], 
        array $existingProductCertificatesMap = [],
        array $productCodeMap = []
    ){
        $path = '/media/Certificates/';
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->productCertificate = new stdClass();

        if(isset($data['product_certificate_id'])){
            $this->productCertificate->product_certificate_id = $this->validateInteger($data['product_certificate_id'], 'product_certificate_id', 0, true);
            if(isset($this->productCertificate->product_certificate_id)){
                $this->isExistingData = true;
            }
        }

        $productCode = isset($data['product_code']) ? strtolower(str_replace(' ', '-', $data['product_code'])) : null;
        if(!$productCode){
            $this->addError('product_code', 'Product code is required');
            return;
        }

        if(!isset($productCodeMap[$productCode])){
            $this->addError('product_code', 'Product not found');
            return;
        }


        //Set manufacturer properties
        $this->productCertificate->product_id = isset($productCodeMap[$productCode]) ? $productCodeMap[$productCode] : 0;
        $this->productCertificate->certificate_type = $this->validateString($data['title'], 'certificate_type', 191, true);
        if(isset($existingProductCertificatesMap[$this->productCertificate->product_id . '-' . $this->productCertificate->certificate_type])){
            $this->productCertificate->product_certificate_id = $existingProductCertificatesMap[$this->productCertificate->product_id . '-' . $this->productCertificate->certificate_type];
            $this->isExistingData = true;
        }
        $this->productCertificate->logo = $this->validateJson($data['logo'], 'logo', $path);
        $this->productCertificate->title = $this->validateString($data['title'], 'title', 191, true);
        $this->productCertificate->certificate_file = $this->validateJson($data['certificate'], 'certificate_file', $path);
        $this->productCertificate->file_format = $this->validateString($data['type'], 'file_format', 191, true);
    }

    public function toArray(): array
    {
        return (array) $this->productCertificate;
    }
}
