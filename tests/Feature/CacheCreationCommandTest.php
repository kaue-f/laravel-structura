<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CacheCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Caches'));
    }

    /**
     * Test cache creation default.
     * Generate an cache class.
     */
    public function test_cache_creation_default(): void
    {
        $this->artisan('structura:cache', [
            'name' => 'SampleCache',
        ])->assertExitCode(0);

        $path = app_path('Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString("protected string \$prefix = 'sample'", File::get($path));
    }

    /**
     * Test cache creation with class extending CacheSupport by default.
     * Generate an cache with class extending CacheSupport.
     */
    public function test_cache_creation_with_cache_support_extension(): void
    {
        $this->artisan('structura:cache', [
            'name' => 'SampleCache',
            '-e' => true,
        ])->assertExitCode(0);

        $path = app_path('Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('extends CacheSupport', File::get($path));
        $this->assertStringContainsString('use KaueF\Structura\Support\CacheSupport;', File::get($path));
        $this->assertStringContainsString("protected string \$prefix = 'sample'", File::get($path));
    }

    /**
     * Test cache creation with raw option.
     * Generate a cache without any method.
     */
    public function test_cache_creation_with_raw_option(): void
    {
        $this->artisan('structura:cache', [
            'name' => 'SampleCache',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
    }
}
