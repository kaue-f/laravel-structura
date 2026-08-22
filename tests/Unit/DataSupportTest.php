<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use KaueF\Structura\Support\DataSupport;
use Tests\TestCase;

readonly class SampleRequestData extends DataSupport
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

class DataSupportTest extends TestCase
{
    public function test_creates_data_from_basic_request(): void
    {
        $request = new Request([
            'name' => 'Basic Name',
            'age' => 25,
            'ignored' => 'should be ignored',
        ]);

        $data = SampleRequestData::fromRequest($request);

        $this->assertEquals('Basic Name', $data->name);
        $this->assertEquals(25, $data->age);
        $this->assertNull($data->optional);
    }

    public function test_creates_data_from_form_request_using_validated(): void
    {
        $request = new FakeFormRequest([
            'name' => 'Dirty Name',
            'age' => 12,
            'ignored' => 'hacker',
        ]);

        $data = SampleRequestData::fromRequest($request);

        $this->assertEquals('Form Name', $data->name);
        $this->assertEquals(99, $data->age);
    }

    public function test_creates_data_from_array(): void
    {
        $array = [
            'name' => 'Array Name',
            'age' => 30,
            'optional' => 'Exists',
        ];

        $data = SampleRequestData::fromArray($array);

        $this->assertEquals('Array Name', $data->name);
        $this->assertEquals(30, $data->age);
        $this->assertEquals('Exists', $data->optional);
    }

    public function test_data_json_serializes_correctly(): void
    {
        $data = SampleRequestData::fromArray([
            'name' => 'John',
            'age' => 50,
        ]);

        $json = json_encode($data);
        $this->assertStringContainsString('"name":"John"', $json);
        $this->assertStringContainsString('"age":50', $json);
    }

    public function test_data_to_array_returns_all_properties(): void
    {
        $data = SampleRequestData::fromArray([
            'name' => 'Alice',
            'age' => 28,
            'optional' => 'present',
        ]);

        $array = $data->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Alice', $array['name']);
        $this->assertEquals(28, $array['age']);
        $this->assertEquals('present', $array['optional']);
    }
}
