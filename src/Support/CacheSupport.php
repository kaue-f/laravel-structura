<?php

namespace KaueF\Structura\Support\Cache;

use InvalidArgumentException;
use Illuminate\Support\Facades\Cache;
use KaueF\Structura\Contracts\Cache\CacheInterface;

abstract class CacheSupport implements CacheInterface
{
    /**
     * Cache key prefix.
     *  
     * @var string
     */
    protected string $prefix = '';

    /**
     * Default cache TTL (Time To Live) in seconds.
     * 
     * @var int
     */
    protected int $ttl = 3600;

    /**
     * Build a cache key with the defined prefix.
     *
     * @param  string $key
     * @return string
     */
    protected function key(string $key): string
    {
        return ($this->prefix)
            ? "{$this->prefix}:{$key}"
            : $key;
    }

    /**
     * Validate the TTL (Time To Live) value.
     * 
     * Ensures the TTL is a non-negative integer.
     *
     * @param  int $ttl
     * @return int
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateTtl(int $ttl): int
    {
        if ($ttl < 0) {
            throw new InvalidArgumentException('TTL must be a positive integer');
        }

        return $ttl;
    }

    /**
     * Retrieve an item from the cache or store it if it does not exist.
     *
     * @param  string $key
     * @param  \Closure $callback
     * @return mixed
     */
    public function remember(string $key, \Closure $callback): mixed
    {
        return Cache::remember(
            key: $this->key($key),
            ttl: $this->validateTtl(ttl: $this->ttl),
            callback: $callback
        );
    }

    /**
     * Retrieve an item from the cache.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get(
            key: $this->key($key),
            default: $default
        );
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int|null $seconds
     * @return bool
     */
    public function put(string $key, mixed $value, int|null $seconds = null): bool
    {
        return Cache::put(
            key: $this->key($key),
            value: $value,
            ttl: $this->validateTtl(ttl: $seconds ?? $this->ttl)
        );
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     */
    public function forever(string $key, mixed $value): bool
    {
        return Cache::forever(
            key: $this->key($key),
            value: $value
        );
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has(key: $this->key($key));
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget(key: $this->key($key));
    }
}
