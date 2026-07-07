<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;
use App\Core\Exceptions\ValidationException;
use PhpParser\Node\Name;
use stdClass;

class UserGroup extends Model
{
    public ?int $user_group_id = null;
    public ?int $status = null;
    public ?string $code = null;
    public ?int $sort_order = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the content for this user group
     * 
     * @return array
     */
    public function userGroupContent()
    {
        return $this->hasOne(UserGroupContent::class, 'user_group_id', 'user_group_id');
    }
}

class UserGroupResponse
{
    public ?int $user_group_id;
    public ?int $status;
    public ?string $code;
    public ?int $sort_order;
    public ?string $created_at;
    public ?string $updated_at;
    
    public UserGroupContentData $userGroupContent;

    public function __construct(stdClass $data) 
    {
        $this->user_group_id = $data->user_group_id ?? null;
        $this->code = $data->code ?? null;
        $this->status = $data->status ?? null;
        $this->sort_order = $data->sort_order ?? null;
        $this->created_at = $data->created_at ?? null;
        $this->updated_at = $data->updated_at ?? null;

        if(isset($data->userGroupContent)) {
            $this->userGroupContent = is_array($data->userGroupContent) 
                ? new UserGroupContentData($data->userGroupContent) 
                : new UserGroupContentData(json_decode($data->userGroupContent, true));
        } else {
            $this->userGroupContent = new UserGroupContentData([]);
        }
    }

    public function toArray(): array
    {
        return [
            'user_group_id' => $this->user_group_id,
            'code' => str_replace(' ', '-', strtolower(trim($this->userGroupContent->name))) ?? null,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'userGroupContent' => $this->userGroupContent->toArray(),
        ];
    }
}

class UserGroupData {
    public ?int $user_group_id;
    public ?string $code;
    public ?int $status;
    public ?int $sort_order;
    public ?UserGroupContentData $userGroupContent;

    public function __construct(array $data, bool $isUpdate = false) 
    {
        // Validate required fields for create
        if (!$isUpdate) {
            $errors = [];
            
            if (!isset($data['status'])) {
                $errors['status'] = ['Status is required'];
            }
            
            if (!isset($data['sort_order'])) {
                $errors['sort_order'] = ['Sort order is required'];
            }
            
            if (!isset($data['userGroupContent'])) {
                $errors['userGroupContent'] = ['User group content is required'];
            }
            
            if (!empty($errors)) {
                throw new ValidationException($errors, 'Validation failed');
            }

            // Validate userGroupContent structure for create
            if (isset($data['userGroupContent'])) {
                $contentErrors = [];
                
                if (!isset($data['userGroupContent']['name'])) {
                    $contentErrors['name'] = ['Name is required'];
                }
                
                if (!isset($data['userGroupContent']['content'])) {
                    $contentErrors['content'] = ['Content is required'];
                }
                
                if (!isset($data['userGroupContent']['language_id'])) {
                    $contentErrors['language_id'] = ['Language ID is required'];
                }
                
                if (!empty($contentErrors)) {
                    $errors['userGroupContent'] = $contentErrors;
                }
            }
            
            if (!empty($errors)) {
                throw new ValidationException($errors, 'Validation failed');
            }
        }

        // Set properties
        if(isset($data['user_group_id'])) $this->user_group_id = $data['user_group_id'];
        if(isset($data['userGroupContent']['name'])) $this->code = str_replace(' ', '-', strtolower(trim($data['userGroupContent']['name'])));
        if(isset($data['status'])) $this->status = (int)$data['status'];
        if(isset($data['sort_order'])) $this->sort_order = (int)$data['sort_order'];
        if(isset($data['userGroupContent'])) $this->userGroupContent = new UserGroupContentData($data['userGroupContent']);
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->user_group_id)) $data['user_group_id'] = $this->user_group_id;
        if(isset($this->code)) $data['code'] = $this->code;
        if(isset($this->status)) $data['status'] = $this->status;
        if(isset($this->sort_order)) $data['sort_order'] = $this->sort_order;
        if(isset($this->userGroupContent)) {
            $data['name'] = $this->userGroupContent->name;
            $data['content'] = $this->userGroupContent->content;
            $data['language_id'] = $this->userGroupContent->language_id;
        }
        
        return $data;
    }
}

class UserGroupContentData {
    public ?string $name = null;
    public ?string $content = null;
    public ?int $language_id = null;
    public ?int $user_group_id = null;

    public function __construct(array $data)
    {
        if(isset($data['name'])) $this->name = $data['name'];
        if(isset($data['content'])) $this->content = $data['content'];
        if(isset($data['language_id'])) $this->language_id = (int)$data['language_id'];
        if(isset($data['user_group_id'])) $this->user_group_id = $data['user_group_id'];
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->name)) $data['name'] = $this->name;
        if(isset($this->content)) $data['content'] = $this->content;
        if(isset($this->language_id)) $data['language_id'] = $this->language_id;
        if(isset($this->user_group_id)) $data['user_group_id'] = $this->user_group_id;
        
        return $data;
    }
} 