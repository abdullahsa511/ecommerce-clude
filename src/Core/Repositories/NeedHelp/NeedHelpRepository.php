<?php

declare(strict_types=1);

namespace App\Core\Repositories\NeedHelp;

use App\Core\Models\NeedHelp\NeedHelp;
use App\Core\Repositories\Base\BaseRepository;

use PDO;

class NeedHelpRepository extends BaseRepository implements NeedHelpRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'need_help', NeedHelp::class);
    }



    public function getNeedHelpComponentData(array $param)
    {
        $model = $this->model::class;
        //Now validate if the $params['model'] is a valid model
        if(isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if(isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }
            if(isset($param['fields']) && is_array($param['fields'])) {
                $query->select($param['fields']);
            }
            $result = $query->findAll();
            return $result;
        }
        return [];
    }
} 