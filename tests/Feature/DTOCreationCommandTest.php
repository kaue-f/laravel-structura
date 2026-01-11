<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class DTOCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('DTOs'));
    }

    public function test_dto_creation_default(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('final readonly class SampleDTO', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_dto_creation_no_final(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
            '--no-final' => true
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('readonly class SampleDTO', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_dto_creation_no_readonly(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
            '--no-readonly' => true
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('final class SampleDTO', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_dto_creation_no_construct(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
            '--no-construct' => true
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('final readonly class SampleDTO', File::get($path));
        $this->assertStringContainsString('//', File::get($path));
    }

    public function test_dto_creation_with_trait(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
            '-t' => true
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('final readonly class SampleDTO', File::get($path));
        $this->assertStringContainsString('use KaueF\Structura\Concerns\InteractsWithDTO;', File::get($path));
        $this->assertStringContainsString('use InteractsWithDTO', File::get($path));
    }

    /**
     * Test DTO creation with raw option.
     * Generate a DTO without helpers or modifiers
     * 
     * @return void
     */
    public function test_dto_creation_with_raw_option(): void
    {
        $this->artisan('structura:dto', [
            'name' => 'SampleDTO',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('DTOs/SampleDTO.php');
        $this->assertTrue(File::exists($path));
    }
}
