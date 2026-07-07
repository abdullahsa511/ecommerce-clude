<?php

declare(strict_types=1);

namespace App\Core\Validation;

use PHPUnit\Runner\Baseline\Issue;
use stdClass;

class UserGroupDataValidation extends Validation
{
    public stdClass $user_group;
    public stdClass $user_group_content;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ['userGroupIds' => [], 'languageMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->user_group = new stdClass();
        $this->user_group_content = new stdClass();

        if(isset($data['user_group_id'])) {
            $this->user_group_content->user_group_id = $this->validateInteger($data['user_group_id'], 'user_group_id', 0, true);
            if(isset($existingData['userGroupIds'][$data['user_group_id']])) {
                $this->isExistingData = true;
            }
        }

        // USER GROUP CONTENT TABLE
        if(isset($data['name']) && !empty($data['name']) && $data['name']){
            // USER GROUP TABLE
            $this->user_group->sort_order = $this->validateInteger($data['sort_order'] ?? 1, 'sort_order', 0, true);
            $this->user_group->status = $this->validateInteger($data['status'] ?? 1, 'status', 0, true);
            // USER GROUP CONTENT TABLE
            $this->user_group_content->name = $this->validateString($data['name'], 'name', 191);
            $this->user_group_content->content = isset($data['content']) && !empty($data['content']) && $data['content'] ? $this->validateString($data['content'], 'content', 191) : $this->user_group_content->name;
            $this->user_group_content->language_id = isset($data['language_code']) ? $existingData['languageMap'][$data['language_code']] : 1;
            $code = str_replace(' ', '-', strtolower(trim($this->user_group_content->name)));
            if(isset($existingData['userGroupIds'][$code])){
                $this->user_group_content->user_group_id = $existingData['userGroupIds'][$code];
                $this->isExistingData = true;
            }else{
                if(!count($this->errors)){
                    // USER GROUP CODE
                    $this->user_group->code = $code;
                    if(!$this->isExistingData){
                        $this->user_group_content->code = $this->user_group->code;
                    }
                }
            };
        }else{
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'user_group' => (array)$this->user_group,
            'user_group_content' => (array)$this->user_group_content,
        ];
    }
}
