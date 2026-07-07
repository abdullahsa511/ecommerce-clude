<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;
use function App\Core\System\utils\generateUuidV4;
use function App\Core\System\utils\uuidToBin;

class UserDataValidation extends Validation
{
    public stdClass $user;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ["userGroupMap" => [], 'userMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->user = new stdClass();

        // USER ID
        if (isset($data['user_id'])) {
            $this->user->user_id = $this->validateInteger($data['user_id'], 'user_id', 0, true);
            if (isset($existingData['userMap'][$data['user_id']])) {
                $this->user->user_id = $existingData['userMap'][$data['user_id']];
                $this->isExistingData = true;
            }
        }
        // USER DATA
        if (isset($data['username']) && !empty($data['username']) && $data['username']) {
            // mandatory fields
            $this->user->user_group_id = isset($existingData['userGroupMap'][$data['user_group']]) ? $existingData['userGroupMap'][$data['user_group']] : 1;
            $this->user->site_id = 1;
            $this->user->uuid = uuidToBin(generateUuidV4());
            $this->user->username = $this->validateString($data['username'], 'username', 191, true);
            $this->user->first_name = $this->validateString($data['first_name'], 'first_name', 191, true);
            $this->user->last_name = $this->validateString($data['last_name'], 'last_name', 191, true);
            $this->user->password = $this->validateString($data['password'], 'password', 191, true);
            $this->user->email = $this->validateString($data['email'], 'email', 191, true);
            $this->user->phone_number = $this->validateString($data['phone_number'], 'phone_number', 191, true);
            $this->user->url = $this->validateString($data['url'], 'url', 191, true);
            $this->user->status = $this->validateInteger($data['status'], 'status', 0, true);
            $this->user->display_name = $this->validateString($data['display_name'], 'display_name', 191, true);
            // avatar validation
            $folder = '/media/users/';
            $avatar = isset($data['avatar']) ? $this->validateString($data['avatar'], 'avatar', 191) : 'default-avatar.jpg';
            $this->user->avatar = $folder . $avatar;
            // end of avatar
            // optional fields
            $this->user->bio = $this->validateString($data['bio'], 'bio', 191);
            $this->user->token = $this->validateString($data['token'], 'token', 191);
            $this->user->subscribe = $this->validateInteger($data['subscribe'], 'subscribe', 0, true);

            if (isset($existingData['userMap'][$this->user->email])) {
                $this->user->user_id = $existingData['userMap'][$this->user->email];
                $this->isExistingData = true;
            }
        } else {
            $this->addError('username', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'user' => (array)$this->user,
        ];
    }
}
