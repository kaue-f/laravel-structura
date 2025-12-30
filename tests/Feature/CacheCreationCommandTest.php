<?php

namespace Tests\Feature\Caches;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class CacheCreationCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Caches'));
    }

    /**
     * Test cache creation default.
     * Generate an cache class.
     * 
     * @return void
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
     * 
     * @return void
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
        $this->assertStringContainsString('use KaueF\Structura\Support\Cache\CacheSupport;', File::get($path));
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
        $this->artisan('structura:cache', [
            'name' => 'SampleCache',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Caches/SampleCache.php');
        $this->assertTrue(File::exists($path));
    }
}
