<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\User\UserAddress;
use PDO;
class UserAddressRepository extends BaseRepository implements UserAddressRepositoryInterface
{

    public function __construct(PDO $db)
    {
        parent::__construct($db, 'user_address', UserAddress::class);
    }


    public function getAll(int $siteId, int $userId, int $start, int $limit): array
    {
        $query = $this->model->where('user_id', (string)$userId);

        $total = $query->countAll();
        $list = $query->limit($limit)->offset($start)->orderBy('user_address_id', 'DESC')->findAll();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    public function get(int $userAddressId, int $userId): ?UserAddress
    {
        return $this->model->where('user_address_id', (string)$userAddressId)
            ->where('user_id', (string)$userId)
            ->find('user_address_id');
    }

    
} 