<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Services'));
    }

    /**
     * Test service creation with __construct method by default.
     * Generate a service with __construct method.
     */
    public function test_service_creation_with_construct_method_by_default(): void
    {
        $this->artisan('structura:service', [
            'name' => 'SampleService',
            '-c' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Services/SampleService.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('public function __construct()', File::get($path));
    }

    /**
     * Test service creation with raw option.
     * Generate a service without any method.
     */
    public function test_service_creation_with_raw_option(): void
    {
        $this->artisan('structura:service', [
            'name' => 'SampleService',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Services/SampleService.php');
        $this->assertTrue(File::exists($path));
    }
}
