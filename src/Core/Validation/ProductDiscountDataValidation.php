<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProductDiscountDataValidation extends Validation
{
    public stdClass $productDiscount;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ["existsDiscount" => [], "existsProduct" => [], "existsGroup" => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);

        $this->productDiscount = new stdClass();

        // CHECK IF PRODUCT DISCOUNT ID EXISTS
        if (isset($data['product_discount_id'])) {
            $this->lengthType->product_discount_id = $this->validateInteger($data['product_discount_id'], 'product_code', 0);
            if (isset($existingData['productIds'][$data['product_discount_id']])) {
                $this->isExistingData = true;
            }
        }
        // PRODUCT DISCOUNT TABLE
        if (isset($data['product_code']) && !empty($data['product_code']) && $data['product_code']) {
            $product_code = $this->validateString($data['product_code'], 'product_code', 191, true);
            $customer_group = $this->validateString($data['customer_group'], 'customer_group', 191, true);
            // mandatory fields .. 
            $this->productDiscount->product_id = isset($existingData['existsProduct'][$product_code]) ? $existingData['existsProduct'][$product_code] : null;
            if(!$this->productDiscount->product_id){
                $this->addError('product_code', 'product not found');
            }
            $this->productDiscount->user_group_id = isset($existingData['existsGroup'][$customer_group]) ? $existingData['existsGroup'][$customer_group] : null;
            if(!$this->productDiscount->user_group_id){
                $this->addError('customer_group', 'user group not found');
            }
            // optional fields .. 
            $this->productDiscount->price = isset($data['discounted_price']) ? $this->validateInteger($data['discounted_price'], 'discounted_price') : 0;
            $this->productDiscount->from_date = isset($data['from_date']) ? $this->validateDate($data['from_date'], 'from_date', 20) ?? null : null;
            $this->productDiscount->to_date = isset($data['end_date']) ? $this->validateDate($data['end_date'], 'end_date', 20) ?? null : null;
            // check if from_date and end_date are valid .. 
            if ($data['from_date'] && $data['end_date']) {
                if (strtotime($data['end_date']) < strtotime($data['from_date'])) {
                    $this->addError('end_date', 'end_date cannot be earlier than from_date');
                }
            }
            // CHECK IF PRODUCT DISCOUNT EXISTS
            // unique identifier .. 
            $uniqueIdentifier = $this->productDiscount->product_id . '-' . $this->productDiscount->user_group_id . '-' . $this->productDiscount->from_date . '-' . $this->productDiscount->to_date;
            if (isset($existingData['existsDiscount'][$uniqueIdentifier])) {
                $this->productDiscount->product_discount_id = $existingData['existsDiscount'][$uniqueIdentifier];
                $this->isExistingData = true;
            }
        } else {
            $this->addError('product_code', 'product code is required');
        }
    }

    public function toArray(): array
    {
        return [
            'product_discount' => (array)$this->productDiscount,
        ];
    }
}
