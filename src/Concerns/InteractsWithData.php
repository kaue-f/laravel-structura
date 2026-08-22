<?php

namespace KaueF\Structura\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use ReflectionClass;

trait InteractsWithData
{
    /**
     * Create a Data object using its constructor arguments.
     */
    public static function make(mixed ...$arguments): static
    {
        return new static(...$arguments);
    }

    /**
     * Create a Data object from an array, Laravel Arrayable object, or request.
     *
     * @param  array<string, mixed>|Arrayable<array-key, mixed>|Request  $source
     */
    public static function from(array|Arrayable|Request $source): static
    {
        if ($source instanceof Request) {
            return static::fromRequest($source);
        }

        return static::fromArray(is_array($source) ? $source : $source->toArray());
    }

    /**
     * Create a Data object from a request.
     *
     * Form requests expose validated input; ordinary requests provide all input.
     */
    public static function fromRequest(Request $request): static
    {
        $data = method_exists($request, 'validated') ? $request->validated() : $request->all();

        return static::fromArray($data);
    }

    /**
     * Create a Data object by mapping array keys to constructor parameter names.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        if (! $constructor->isPublic()) {
            throw new InvalidArgumentException(sprintf(
                'The constructor for [%s] must be public to create it from data.',
                static::class,
            ));
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();

            if ($parameter->isVariadic()) {
                if (! array_key_exists($name, $data)) {
                    continue;
                }

                if (! is_array($data[$name])) {
                    throw new InvalidArgumentException(sprintf(
                        'The variadic parameter [$%s] for [%s] must be an array.',
                        $name,
                        static::class,
                    ));
                }

                array_push($arguments, ...$data[$name]);

                continue;
            }

            if (array_key_exists($name, $data)) {
                $arguments[] = $data[$name];

                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();

                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Missing required data key [%s] for [%s].',
                $name,
                static::class,
            ));
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @throws \JsonException
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options | JSON_THROW_ON_ERROR);
    }
}
