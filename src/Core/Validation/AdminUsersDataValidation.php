<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class AdminUsersDataValidation extends Validation
{
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ['adminIds' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $mediaPath = 'media/admins/';

        if (isset($data['admin_id'])) {
            $this->rawData['admin_id'] = $this->validateInteger($data['admin_id'], 'admin_id', 0, true);
            if (isset($existingData['adminIds'][$this->rawData['admin_id']])) {
                $this->isExistingData = true;
            }
        }
        if (isset($data['username']) && !empty($data['username']) && $data['username']) {
            if (empty($data['email']) || empty($data['username'])) {
                $this->addError('email', 'Email and username are required');
            }
            if (isset($existingData['adminIds'][$this->rawData['username']])) {
                $this->rawData['username'] = $existingData['adminIds'][$this->rawData['username']];
                $this->isExistingData = true;
            } else {
                if (!count($this->errors)) {
                    // username
                    $this->rawData['username'] = $this->validateString($data['username'], 'username', 191);
                    // first_name
                    $this->rawData['first_name'] = $this->validateString($data['first_name'], 'first_name', 191);
                    // last_name
                    $this->rawData['last_name'] = $this->validateString($data['last_name'], 'last_name', 191);
                    // password
                    $this->rawData['password'] = $this->validateString($data['password'], 'password', 191);
                    // email
                    // if (empty($data['email']) || empty($data['username'])) {
                    //     $this->addError('email', 'Email and username are required');
                    // } else {
                    $this->rawData['email'] = $this->validateEmail($data['email'], 'email', 191);
                    // }
                    // phone_number
                    $this->rawData['phone_number'] = $this->validateString($data['phone_number'], 'phone_number', 191);
                    // url
                    $this->rawData['url'] = $this->validateString($data['url'], 'url', 191);
                    // $this->rawData['url'] = $this->validateUrl($data['url'], 'url', 191);
                    // display_name
                    $this->rawData['display_name'] = $this->validateString($data['display_name'], 'display_name', 191);
                    // avatar
                    $avatar = isset($data['avatar']) ? $this->validateString($data['avatar'], 'avatar', 191) : 'default-avatar.jpg';
                    $this->rawData['avatar'] = $mediaPath . $avatar;
                    // bio
                    $this->rawData['bio'] = $this->validateString($data['bio'], 'bio', 191);
                    // role_id
                    $this->rawData['role_id'] = $this->validateInteger($data['role_id'], 'role_id', 0);
                    // site_access
                    $this->rawData['site_access'] = $this->validateString($data['site_access'], 'site_access', 191);
                    // status
                    $this->rawData['status'] = $this->validateInteger($data['status'], 'status', 0);
                    // token
                    $this->rawData['token'] = $this->validateString($data['token'], 'token', 191);

                    if (!$this->isExistingData) {
                        $this->isExistingData = false;
                    }
                }
            };
        } else {
            $this->addError('username', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'data' => (array)$this->rawData,
        ];
    }
}
