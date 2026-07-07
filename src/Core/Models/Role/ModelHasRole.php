<?php

declare(strict_types=1);

namespace App\Core\Models\Role;

use App\Core\Models\Base\Model;

class ModelHasRole extends Model
{
    protected string $table = 'model_has_role';
    protected string $tableAlias = 'model_has_role';

    /**
     * Get the primary key for this model
     */
    public function getPrimaryKey(): string
    {
        // Since this table has a composite primary key, we'll use model_id as the main identifier
        return 'model_id';
    }

    /**
     * Get the composite primary key columns
     */
    public function getCompositePrimaryKey(): array
    {
        return ['model_id', 'model_type', 'role_id'];
    }

    /**
     * Get the model ID
     */
    public function getModelId(): ?int
    {
        return $this->model_id ?? null;
    }

    /**
     * Set the model ID
     */
    public function setModelId(int $modelId): self
    {
        $this->model_id = $modelId;
        return $this;
    }

    /**
     * Get the model type
     */
    public function getModelType(): ?string
    {
        return $this->model_type ?? null;
    }

    /**
     * Set the model type
     */
    public function setModelType(string $modelType): self
    {
        $this->model_type = $modelType;
        return $this;
    }

    /**
     * Get the role ID
     */
    public function getRoleId(): ?int
    {
        return $this->role_id ?? null;
    }

    /**
     * Set the role ID
     */
    public function setRoleId(int $roleId): self
    {
        $this->role_id = $roleId;
        return $this;
    }
} 