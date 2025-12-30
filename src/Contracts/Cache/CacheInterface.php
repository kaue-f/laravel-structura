<?php

namespace KaueF\Structura\Contracts\Cache;

interface CacheInterface
{
    public function remember(string $key, \Closure $callback): mixed;

    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, int|null $seconds = null): bool;

    public function forever(string $key, mixed $value): bool;

    public function has(string $key): bool;

    public function forget(string $key): bool;
}
