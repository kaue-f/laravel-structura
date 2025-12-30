<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ActionCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Actions'));
    }

    /**
     * Test action creation with execute method by default.
     * Generate an action with execute method.
     *
     * @return void
     */
    public function test_action_creation_with_execute_method_by_default(): void
    {
        $this->artisan('structura:action', [
            'name' => 'SampleAction',
            '-e' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Actions/SampleAction.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('public function execute()', File::get($path));
    }

    /**
     * Test action creation with handle method.
     * Generate an action with handle method.
     *
     * @return void
     */
    public function test_action_creation_with_handle_method(): void
    {
        $this->artisan('structura:action', [
            'name' => 'SampleAction',
            '-l' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Actions/SampleAction.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('public function handle()', File::get($path));
    }

    /**
     * Test action creation with invokable method.
     * Generate an action with __invoke method.
     *
     * @return void
     */
    public function test_action_creation_with_invokable_method(): void
    {
        $this->artisan('structura:action', [
            'name' => 'SampleAction',
            '-i' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Actions/SampleAction.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('public function __invoke()', File::get($path));
    }


    /**
     * Test action creation with construct method.
     * Gererate an action with __construct method.
     *
     * @return void
     */
    public function test_action_creation_with_construct_method(): void
    {
        $this->artisan('structura:action', [
            'name' => 'SampleAction',
            '-c' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Actions/SampleAction.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('public function __construct()', File::get($path));
    }


    /**
     * Test action creation with raw option.
     * Generate an action without any method.
     * 
     * @return void
     */
    public function test_action_creation_with_raw_option(): void
    {
        $this->artisan('structura:action', [
            'name' => 'SampleAction',
            '-r' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Actions/SampleAction.php');
        $this->assertTrue(File::exists($path));
    }
}
