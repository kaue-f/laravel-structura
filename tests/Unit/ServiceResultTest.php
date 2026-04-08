<?php

namespace Tests\Unit;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KaueF\Structura\Support\ServiceResult;
use Tests\TestCase;

class ServiceResultTest extends TestCase
{
    public function test_can_create_successful_result()
    {
        $result = ServiceResult::success(['id' => 1], 'User created', 201);

        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isFail());
        $this->assertEquals(['id' => 1], $result->data);
        $this->assertEquals('User created', $result->message);
        $this->assertEquals(201, $result->status);
    }

    public function test_can_create_failed_result()
    {
        $result = ServiceResult::fail('Validation failed', 422, ['field' => 'Required']);

        $this->assertFalse($result->isSuccess());
        $this->assertTrue($result->isFail());
        $this->assertEquals(['field' => 'Required'], $result->data);
        $this->assertEquals('Validation failed', $result->message);
        $this->assertEquals(422, $result->status);
    }

    public function test_default_values_are_correct()
    {
        // Default Success
        $success = ServiceResult::success();
        $this->assertTrue($success->isSuccess());
        $this->assertNull($success->data);
        $this->assertNull($success->message);
        $this->assertEquals(200, $success->status);

        // Default Fail
        $fail = ServiceResult::fail();
        $this->assertTrue($fail->isFail());
        $this->assertNull($fail->data);
        $this->assertNull($fail->message);
        $this->assertEquals(400, $fail->status);
    }

    public function test_to_response_formats_json_correctly()
    {
        $request = Request::create('/', 'GET');
        $result = ServiceResult::success(['id' => 99], 'Magic Success', 201);

        $response = $result->toResponse($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());

        // Decoding JSON payload
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Magic Success', $data['message']);
        $this->assertEquals(['id' => 99], $data['data']);
    }
}
