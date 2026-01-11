<?php

namespace KaueF\Structura\Concerns;

use ReflectionClass;

trait InteractsWithDTO
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
     * @return string JSON representation of the DTO.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
