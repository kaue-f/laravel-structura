<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DataCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Data'));
    }

    public function test_data_creation_default(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringContainsString('final readonly class SampleData', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_data_creation_no_final(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
            '--no-final' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringContainsString('readonly class SampleData', File::get($path));
        $this->assertStringNotContainsString('final readonly class SampleData', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_data_creation_no_readonly(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
            '--no-readonly' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringContainsString('final class SampleData implements JsonSerializable', File::get($path));
        $this->assertStringNotContainsString('readonly class SampleData', File::get($path));
        $this->assertStringContainsString('use InteractsWithData;', File::get($path));
        $this->assertStringContainsString('public function __construct', File::get($path));
    }

    public function test_data_creation_no_construct(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
            '--no-construct' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringContainsString('final readonly class SampleData', File::get($path));
        $this->assertStringContainsString('//', File::get($path));
    }

    public function test_data_creation_uses_the_data_trait(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringContainsString('final readonly class SampleData implements JsonSerializable', File::get($path));
        $this->assertStringContainsString('use KaueF\\Structura\\Concerns\\InteractsWithData;', File::get($path));
    }

    /**
     * Test Data creation with raw option.
     * Generates a Data class without helpers or modifiers.
     */
    public function test_data_creation_with_raw_option(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Data/SampleData.php');
        $this->assertTrue(File::exists($path));
        $this->assertValidPhp($path);
        $this->assertStringNotContainsString('final', File::get($path));
        $this->assertStringNotContainsString('readonly', File::get($path));
    }

    public function test_data_creation_raw_cannot_be_combined_with_other_options(): void
    {
        $this->artisan('structura:data', [
            'name' => 'Sample',
            '-r' => true,
            '--no-final' => true,
        ])
            ->assertExitCode(1);
    }
}
