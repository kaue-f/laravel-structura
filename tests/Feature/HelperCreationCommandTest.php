<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class HelperCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Helpers'));
    }

    /**
     * Test helper creation with example method by default.
     * Generate a helper with example method.
     * 
     * @return void
     */
    public function test_helper_creation_with_example_method_by_default(): void
    {
        $this->artisan('structura:helper', [
            'name' => 'SampleHelper',
            '-e' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/SampleHelper.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString("public static function example(mixed \$value)", File::get($path));
    }

    /**
     * Test helper creation from package stub.
     * Generate a helper global.
     * 
     * @return void 
     */
    public function test_helper_creation_from_package_stub(): void
    {
        $this->artisan('structura:helper', [
            '-s' => true
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/helpers.php');
        $composer_path = base_path('composer.json');

        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString("function isNullOrEmpty(mixed \$value)", File::get($path));
        $this->assertStringContainsString("app\/Helpers\/helpers.php", File::get($composer_path));
    }

    /**
     * Test helper creation with global option.
     * Generate a helper global.
     * 
     * @return void
     */
    public function test_helper_creation_global(): void
    {
        $this->artisan('structura:helper', [
            'name' => 'SampleHelper',
            '-g' => true
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/sample_helper.php');
        $composer_path = base_path('composer.json');

        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('<?php', File::get($path));
        $this->assertStringContainsString("app\/Helpers\/sample_helper.php", File::get($composer_path));
    }

    /**
     * Test helper creation with raw option.
     * Generate a helper without any method.
     *
     * @return void
     */
    public function test_helper_creation_with_raw_option(): void
    {
        $this->artisan('structura:helper', [
            'name' => 'SampleHelper',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Helpers/SampleHelper.php');
        $this->assertTrue(File::exists($path));
    }
}
