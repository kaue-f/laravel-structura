<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use KaueF\Structura\Support\DataSupport;
use Tests\TestCase;

readonly class UserProfileData extends DataSupport
{
    public function __construct(
        public string $name,
        public int|string $identifier,
        public bool $active = true,
    ) {}
}

readonly class EmptyData extends DataSupport {}

readonly class TagsData extends DataSupport
{
    /** @var list<string> */
    public array $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }
}

class ValidatedRequest extends Request
{
    public function validated($key = null, $default = null): array
    {
        return ['name' => 'Validated User', 'identifier' => 42];
    }
}

class DataTest extends TestCase
{
    public function test_creates_data_from_array_and_preserves_constructor_defaults(): void
    {
        $data = UserProfileData::fromArray(['name' => 'Jane Doe', 'identifier' => 'user-1']);

        $this->assertSame('Jane Doe', $data->name);
        $this->assertSame('user-1', $data->identifier);
        $this->assertTrue($data->active);
    }

    public function test_creates_data_from_a_request_and_uses_validated_input_when_available(): void
    {
        $data = UserProfileData::fromRequest(new ValidatedRequest([
            'name' => 'Untrusted User',
            'identifier' => 'untrusted',
        ]));

        $this->assertSame('Validated User', $data->name);
        $this->assertSame(42, $data->identifier);
    }

    public function test_creates_data_from_a_laravel_collection(): void
    {
        $data = UserProfileData::from(new Collection([
            'name' => 'Collection User',
            'identifier' => 10,
        ]));

        $this->assertSame('Collection User', $data->name);
        $this->assertSame(10, $data->identifier);
    }

    public function test_creates_data_without_a_constructor(): void
    {
        $this->assertInstanceOf(EmptyData::class, EmptyData::fromArray([]));
    }

    public function test_requires_each_non_default_constructor_parameter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required data key [identifier]');

        UserProfileData::fromArray(['name' => 'Jane Doe']);
    }

    public function test_maps_variadic_constructor_parameters_from_an_array(): void
    {
        $data = TagsData::fromArray(['tags' => ['php', 'laravel']]);

        $this->assertSame(['php', 'laravel'], $data->tags);
    }

    public function test_serializes_data_natively_and_with_to_json(): void
    {
        $data = UserProfileData::make('Jane Doe', 1);

        $this->assertSame('{"name":"Jane Doe","identifier":1,"active":true}', json_encode($data));
        $this->assertSame("{\n    \"name\": \"Jane Doe\",\n    \"identifier\": 1,\n    \"active\": true\n}", $data->toJson(JSON_PRETTY_PRINT));
    }
}
