<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class TraitCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Concerns'));
    }

    /**
     * Test trait creation.
     *
     * @return void
     */
    public function test_trait_creation(): void
    {
        $this->artisan('structura:trait', [
            'name' => 'Sample',
        ])->assertExitCode(0);

        $path = app_path('Concerns/Sample.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('trait Sample', File::get($path));
    }
}
