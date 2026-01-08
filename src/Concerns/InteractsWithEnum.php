<?php

namespace KaueF\Structura\Concerns;

use UnitEnum;
use KaueF\Structura\Support\EnumSupport;

trait InteractsWithEnum
{
    public static function toArray(): array
    {
        return EnumSupport::toArray(enum: static::class);
    }

    public static function toData(string $labelMethod = 'label', ?callable $callback = null, string $sortBy = 'name', $order = 'asc'): array
    {
        return EnumSupport::toData(
            enum: static::class,
            labelMethod: $labelMethod,
            callback: $callback,
            sortBy: $sortBy,
            order: $order
        );
    }

    public static function labels(?string $order = null): array
    {
        return EnumSupport::labels(enum: static::class, order: $order);
    }

    public static function names(?string $order = null): array
    {
        return EnumSupport::names(enum: static::class, order: $order);
    }

    public static function values(?string $order = null): array
    {
        return EnumSupport::values(enum: static::class, order: $order);
    }

    public static function toJson(): string
    {
        return EnumSupport::toJson(enum: static::class);
    }

    public static function rule(): \Illuminate\Validation\Rules\In
    {
        return EnumSupport::validationRule(enum: static::class);
    }

    public static function fromName(string $name): static
    {
        return EnumSupport::fromName(enum: static::class, name: $name);
    }

    public static function tryFromName(string $name): ?static
    {
        return EnumSupport::tryFromName(enum: static::class, name: $name);
    }

    public static function equals(UnitEnum|string $enum): bool
    {
        return EnumSupport::equals(enum_a: static::class, enum_b: $enum);
    }

    public static function in(array $enum): bool
    {
        return EnumSupport::in(enum: static::class, enum_array: $enum);
    }

    public static function random(null|array|string $except = null): static
    {
        return EnumSupport::random(enum: static::class, except: $except);
    }

    public static function randomValue(null|array|string $except = null): int|string
    {
        return EnumSupport::randomValue(enum: static::class, except: $except);
    }
}
