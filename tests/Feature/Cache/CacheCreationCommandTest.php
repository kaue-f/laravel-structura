<?php

namespace Tests\Feature\Cache;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class CacheCreationCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Services/Caches'));
    }

    /**
     * Test cache creation with class extending CacheService by default
     * Generate an cache with class extending CacheService
     * 
     * @return void
     */
    public function test_cache_creation_with_cache_service_by_default(): void
    {
        $this->artisan('make:cache', [
            'name' => 'SampleCache',
            '--base' => true,
        ])->assertExitCode(0);

        $path = app_path('Services/Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('extends CacheService', File::get($path));
        $this->assertStringContainsString('use Structura\Support\Cache\CacheService;', File::get($path));
        $this->assertStringContainsString("protected string \$prefix = 'sample'", File::get($path));
    }

    /**
     * Test cache creation with raw option.
     * Generate a cache without any method.
     *
     * @return void
     */
    public function test_cache_creation_with_raw_option(): void
    {
        $this->artisan('make:cache', [
            'name' => 'SampleCache',
            '--raw' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Services/Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
    }
}
