<?php

namespace KaueF\Structura\Support;

use Illuminate\Http\Request;
use JsonSerializable;
use ReflectionClass;

abstract readonly class DataSupport implements JsonSerializable
{
    /**
     * Creates the Data object from an Illuminate Request instance.
     * Extracts validated data if it's a FormRequest, otherwise extracts all data.
     */
    public static function fromRequest(Request $request): static
    {
        $data = method_exists($request, 'validated') ? $request->validated() : $request->all();

        return static::fromArray($data);
    }

    /**
     * Creates the Data object from an associative array.
     *
     * Array values are automatically mapped to the constructor parameters
     * using each parameter name as the corresponding key.
     *
     * @param  array<string, mixed>  $data  Data used to create the object.
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->newInstanceArgs(
            array_map(
                fn ($property) => $data[$property->getName()] ?? null,
                $reflection->getConstructor()->getParameters()
            )
        );
    }

    /**
     * Converts the Data object to an associative array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Returns data to be serialized when converting the object to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
