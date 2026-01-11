<?php

namespace KaueF\Structura\Support;

use ReflectionClass;
use JsonSerializable;

abstract readonly class DTOSupport implements JsonSerializable
{
    /**
     * Creates the DTO from an associative array.
     *
     * Array values are automatically mapped to the constructor parameters
     * using each parameter name as the corresponding key.
     *
     * @param array<string, mixed> $data Data used to create the DTO.
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->newInstanceArgs(
            array_map(
                fn($property) => $data[$property->getName()] ?? null,
                $reflection->getConstructor()->getParameters()
            )
        );
    }

    /**
     * Converts the DTO to an associative array.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Returns data to be serialized when converting the object to JSON.
     * 
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
