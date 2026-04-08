<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use KaueF\Structura\Support\DTOSupport;
use Tests\TestCase;

readonly class SampleRequestDTO extends DTOSupport
{
    public function __construct(
        public string $name,
        public int $age,
        public ?string $optional = null,
    ) {}
}

class FakeFormRequest extends Request
{
    public function validated($key = null, $default = null)
    {
        return [
            'name' => 'Form Name',
            'age' => 99,
        ];
    }
}

class DTOSupportTest extends TestCase
{
    public function test_creates_dto_from_basic_request()
    {
        $request = new Request([
            'name' => 'Basic Name',
            'age' => 25,
            'ignored' => 'should be ignored',
        ]);

        $dto = SampleRequestDTO::fromRequest($request);

        $this->assertEquals('Basic Name', $dto->name);
        $this->assertEquals(25, $dto->age);
        $this->assertNull($dto->optional);
    }

    public function test_creates_dto_from_form_request_using_validated()
    {
        $request = new FakeFormRequest([
            'name' => 'Dirty Name',
            'age' => 12,
            'ignored' => 'hacker',
        ]);

        $dto = SampleRequestDTO::fromRequest($request);

        $this->assertEquals('Form Name', $dto->name);
        $this->assertEquals(99, $dto->age);
    }

    public function test_creates_dto_from_array()
    {
        $data = [
            'name' => 'Array Name',
            'age' => 30,
            'optional' => 'Exists',
        ];

        $dto = SampleRequestDTO::fromArray($data);

        $this->assertEquals('Array Name', $dto->name);
        $this->assertEquals(30, $dto->age);
        $this->assertEquals('Exists', $dto->optional);
    }

    public function test_dto_json_serializes_correctly()
    {
        $dto = SampleRequestDTO::fromArray([
            'name' => 'John',
            'age' => 50,
        ]);

        $json = json_encode($dto);
        $this->assertStringContainsString('"name":"John"', $json);
        $this->assertStringContainsString('"age":50', $json);
    }
}
