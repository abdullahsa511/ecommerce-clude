<?php

/**
 * SA Technology
 *
 * Copyright (C) 2025  Shofiul Alam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace App\Core\Components;

use App\Core\System\Component\ComponentBase;
use App\Core\Models\User as UserModel;
use App\Core\System\Event;
use App\Core\Repositories\UserRepositoryInterface;

class User extends ComponentBase {
	public static $defaultOptions = [
		'user_id'  => null,
		'username' => null,
	];

	protected $options = [];
	private UserRepositoryInterface $userRepository;

	public $cacheExpire = 0; //seconds

	public function __construct(
        UserRepositoryInterface $userRepository,
        array $options = []
    ) 
	{
        parent::__construct($options);
		$this->userRepository = $userRepository;
	}

	function cacheKey() {
		//disable caching
		return false;
	}

    public static function getComponentMeta()
    {
        return [
            'name' => 'site',
            'class' => self::class,
            'validOptions' => [
                'user_id',
                'username'
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false
        ];
    }

	function results($params = []) {
		if ($this->options['user_id']) {
			$results = $this->userRepository->find((int)$this->options['user_id']);
		} elseif ($this->options['username']) {
			$results = $this->userRepository->findByEmail($this->options['username']);
		} else {
			$results = UserModel::current();
		}

		if ($results) {
			// Convert User model to array
			if ($results instanceof UserModel) {
				$results = [
					'user_id' => $results->user_id,
					'display_name' => $results->display_name,
					'email' => $results->email
				];
			}
		} else {
			$results = [];
		}

		list($results) = Event::trigger(__CLASS__,__FUNCTION__, $results);

		return $results->toArray();
	}
}
