<?php

namespace KaueF\Structura\Concerns;

use RuntimeException;

trait Makeable
{
    /**
     * Resolve the instance from the container and execute its main method.
     * Searches for execute(), handle() or __invoke().
     * Override by declaring protected string $makeableMethod = 'customMethodName';
     */
    public static function run(mixed ...$arguments): mixed
    {
        $instance = app(static::class);

        $customMethod = property_exists($instance, 'makeableMethod') ? $instance->makeableMethod : null;

        if ($customMethod) {
            if (method_exists($instance, $customMethod)) {
                return $instance->{$customMethod}(...$arguments);
            }
            throw new RuntimeException(sprintf('Action class [%s] does not implement the defined custom method [%s].', static::class, $customMethod));
        }

        if (method_exists($instance, 'execute')) {
            return $instance->execute(...$arguments);
        }

        if (method_exists($instance, 'handle')) {
            return $instance->handle(...$arguments);
        }

        if (is_callable($instance)) {
            return $instance(...$arguments);
        }

        throw new RuntimeException(sprintf(
            'Action class [%s] must implement an execute(), handle() or __invoke() method.',
            static::class
        ));
    }
}
