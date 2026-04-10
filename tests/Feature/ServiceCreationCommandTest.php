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
     * Test service creation by default generates empty class.
     */
    public function test_service_creation_by_default_is_empty(): void
    {
        $this->artisan('structura:service', [
            'name' => 'SampleService',
        ])
            ->assertExitCode(0);

        $path = app_path('Services/SampleService.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringNotContainsString('public function __construct()', File::get($path));
    }

    /**
     * Test service creation with custom method and result.
     */
    public function test_service_creation_with_custom_method_and_result(): void
    {
        $this->artisan('structura:service', [
            'name' => 'SampleService',
            '--method' => 'process',
            '--result' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Services/SampleService.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('use KaueF\Structura\Support\ServiceResult;', File::get($path));
        $this->assertStringContainsString('public function process(): ServiceResult', File::get($path));
        $this->assertStringContainsString('return ServiceResult::success();', File::get($path));
    }
}
