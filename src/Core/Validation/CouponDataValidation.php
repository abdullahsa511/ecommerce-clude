<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class CouponDataValidation extends Validation
{
    public stdClass $coupon;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ["existsCoupon" => [], "existIds" => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);

        $this->coupon = new stdClass();

        if (isset($data['coupon_id'])) {
            $this->coupon->coupon_id = $this->validateInteger($data['coupon_id'], 'code', 0);
            if (isset($existingData['existIds'][$data['coupon_id']])) {
                $this->isExistingData = true;
            }
        }
        // COUPON TABLE
        if (isset($data['code']) && !empty($data['code']) && $data['code']) {
            // mandatory fields
            $this->coupon->name = isset($data['name']) ? $this->validateString($data['name'], 'name', 191, true) : 'test coupon discount';
            $this->coupon->code = isset($data['code']) ? $this->validateString($data['code'], 'code', 191, true) : 'test-coupon-discount';
            $this->coupon->discount = isset($data['discount']) ? $this->validateInteger($data['discount'], 'discount', null, true) : 0;
            $this->coupon->type = isset($data['type']) ? $this->validateString($data['type'], 'type', 1) : 'P';
            $this->coupon->free_shipping = isset($data['free_shipping']) ? $this->validateInteger($data['free_shipping'], 'free_shipping', null, true) : 50;
            $this->coupon->status = isset($data['status']) ? $this->validateInteger($data['status'], 'status', null, true) : 1;
            $this->coupon->coupon_limit = isset($data['coupon_limit']) ? $this->validateInteger($data['coupon_limit'], 'coupon_limit', null, true) : 50;
            $this->coupon->user_limit = isset($data['user_limit']) ? $this->validateInteger($data['user_limit'], 'user_limit', null, true) : 1000;
            $this->coupon->registered_user_only = isset($data['registered_user_only']) ? $this->validateInteger($data['registered_user_only'], 'registered_user_only', null, true) : 1;
            $this->coupon->cart_total_min = isset($data['cart_total_min']) ? $this->validateInteger($data['cart_total_min'], 'cart_total_min', null, true) : 1111;

            // optional fields
            $this->coupon->date_start = $this->validateDate($data['date_start'], 'date_start', 20) ?? null;
            $this->coupon->date_end = $this->validateDate($data['date_end'], 'date_end', 20) ?? null;

            if ($data['date_start'] && $data['date_end']) {
                if (strtotime($data['date_end']) < strtotime($data['date_start'])) {
                    $this->addError('date_end', 'date_end cannot be earlier than date_start');
                    return false;
                }
            }
            $code = $this->coupon->code;
            if (isset($existingData['existsCoupon'][$code])) {
                $this->coupon->coupon_id = $existingData['existsCoupon'][$code];
                $this->isExistingData = true;
            } else {
                $this->coupon->coupon_id = $this->validateInteger($data['coupon_id'] ?? Null, 'coupon_id', 0);
                if (!count($this->errors)) {
                    $this->coupon->code = $code;
                    if (!$this->isExistingData) {
                        $this->coupon->code = $this->coupon->code;
                    }
                }
            };

        } else {
            $this->addError('code', 'code is required');
        }
    }

    public function toArray(): array
    {
        return (array)$this->coupon;
    }
}
