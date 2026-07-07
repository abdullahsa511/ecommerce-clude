<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
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

namespace App\Core\System\Cache;


class Redis {
	private $expire;

	private $redis;

	private $cachePrefix = ''; //'cache.';

	private $connected = false;

	private $unavailableUntil = 0.0;

	private $options = [
		'expire'                  => 3000,
		'prefix'                  => 'krost.',
		'host'                    => 'mvc.redis',
		'port'                    => 6379,
		'timeout'                 => 1.5,
		'read_timeout'            => 1.5,
		'retry_attempts'          => 0,
		'persistent'              => false,
		'circuit_breaker_seconds' => 30,
	];

	public function __construct($options) {
		$this->options = $options + $this->options;

		$this->expire      = $this->options['expire'] ?? $this->expire;
		$this->cachePrefix = $this->options['prefix'] ?? $this->cachePrefix;

		$this->redis = new \Redis();
		$this->connect();
	}

	private function key($namespace, $key = '') {
		return $this->cachePrefix . ($namespace ? ".$namespace" : '') . $key;
	}

	private function circuitKey() {
		return 'krost.redis.cache.unavailable.' . md5(
			(string) $this->options['host'] . ':' . (string) $this->options['port'] . ':' . $this->cachePrefix
		);
	}

	private function apcuAvailable() {
		if (function_exists('apcu_enabled')) {
			return apcu_enabled();
		}

		return function_exists('apcu_fetch') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOL);
	}

	private function readCircuitUntil() {
		if ($this->apcuAvailable()) {
			$success = false;
			$value = apcu_fetch($this->circuitKey(), $success);
			if ($success) {
				return (float) $value;
			}
		}

		return $this->unavailableUntil;
	}

	private function circuitOpen() {
		return $this->readCircuitUntil() > microtime(true);
	}

	private function markUnavailable(\Throwable $e) {
		$cooldown = max(0, (int) ($this->options['circuit_breaker_seconds'] ?? 30));
		$this->connected = false;

		if ($cooldown > 0) {
			$until = microtime(true) + $cooldown;
			$this->unavailableUntil = $until;

			if ($this->apcuAvailable()) {
				apcu_store($this->circuitKey(), $until, $cooldown);
			}
		}

		error_log('Redis cache unavailable: ' . $e->getMessage());
	}

	private function markAvailable() {
		$this->unavailableUntil = 0.0;

		if ($this->apcuAvailable()) {
			apcu_delete($this->circuitKey());
		}
	}

	private function connect() {
		if ($this->circuitOpen()) {
			return false;
		}

		try {
			$host        = (string) $this->options['host'];
			$port        = (int) $this->options['port'];
			$timeout     = (float) $this->options['timeout'];
			$readTimeout = (float) $this->options['read_timeout'];

			if (!empty($this->options['persistent'])) {
				$persistentId = $this->cachePrefix . $host . ':' . $port;
				$this->connected = (bool) $this->redis->pconnect($host, $port, $timeout, $persistentId);
			} else {
				$this->connected = (bool) $this->redis->connect($host, $port, $timeout);
			}

			if ($readTimeout > 0) {
				$this->redis->setOption(\Redis::OPT_READ_TIMEOUT, $readTimeout);
			}

			if (!empty($this->options['password'])) {
				$this->redis->auth($this->options['password']);
			}

			$this->markAvailable();

			return $this->connected;
		} catch (\Throwable $e) {
			$this->markUnavailable($e);

			return false;
		}
	}

	private function disconnect() {
		try {
			$this->redis->close();
		} catch (\Throwable $e) {
			// Ignore close errors; the next operation will reconnect if possible.
		}

		$this->connected = false;
	}

	private function operation($name, callable $callback, $fallback = null) {
		if ($this->circuitOpen()) {
			return $fallback;
		}

		$attempts = max(0, (int) ($this->options['retry_attempts'] ?? 0)) + 1;

		for ($attempt = 1; $attempt <= $attempts; $attempt++) {
			if (!$this->connected && !$this->connect()) {
				return $fallback;
			}

			try {
				return $callback();
			} catch (\Throwable $e) {
				error_log("Redis cache {$name} failed: " . $e->getMessage());
				$this->disconnect();
				$this->markUnavailable($e);
			}
		}

		return $fallback;
	}

	public function get($namespace, $key) {
		$data = $this->operation('get', function () use ($namespace, $key) {
			return $this->redis->get($this->key($namespace, $key));
		});

		if ($data === false || $data === null) {
			return null;
		}

		return json_decode($data, true);
	}

	public function set($namespace, $key, $value, $expire = null) {
		$expire = $expire ?? $this->expire;
		$_key   = $this->key($namespace, $key);

		return $this->operation('set', function () use ($_key, $value, $expire) {
			$status = $this->redis->set($_key, json_encode($value));

			if ($status && $expire) {
				$this->redis->expire($_key, $expire);
			}

			return $status;
		}, false);
	}

	public function getMulti($namespace, $keys, $serverKey = false) {
		$result = [];

		foreach ($keys as $key) {
			$result[$key] = $this->get($namespace, $key);
		}

		return $result;
	}

	public function setMulti($namespace, $items, $expire = null, $serverKey = false) {
		$expire = $expire ?? $this->expire;

		foreach ($items as $key => $value) {
			$this->set($namespace, $key, $value, $expire);
		}
	}

	public function delete($namespace, $key) {
		if ($key) {
			$keys = $this->key($namespace, $key);
		} else {
			if ($namespace) {
				$keys = $this->key($namespace, '*');
			} else {
				$keys = $this->key('*');
			}
		}

		return $this->operation('delete', function () use ($keys) {
			$this->redis->del($keys);

			return true;
		}, false);
	}
}
