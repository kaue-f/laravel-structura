<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HelperCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Helpers'));
    }

    /**
     * Test helper creation by default generates empty class.
     */
    public function test_helper_creation_by_default_is_empty(): void
    {
        $this->artisan('structura:helper', [
            'name' => 'SampleHelper',
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/SampleHelper.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringNotContainsString('public static function example(mixed $value)', File::get($path));
    }

    /**
     * Test helper creation from package stub.
     * Generate a helper global.
     */
    public function test_helper_creation_from_package_stub(): void
    {
        $this->artisan('structura:helper', [
            '-s' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/helpers.php');
        $composer_path = base_path('composer.json');

        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('function isNullOrEmpty(mixed $value)', File::get($path));
        $this->assertStringContainsString("app\/Helpers\/helpers.php", File::get($composer_path));
    }

    /**
     * Test helper creation with global option.
     * Generate a helper global.
     */
    public function test_helper_creation_global(): void
    {
        $this->artisan('structura:helper', [
            'name' => 'SampleHelper',
            '-g' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/sample_helper.php');
        $composer_path = base_path('composer.json');

        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('<?php', File::get($path));
        $this->assertStringContainsString("app\/Helpers\/sample_helper.php", File::get($composer_path));
    }
}
