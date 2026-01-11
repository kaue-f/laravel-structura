<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class EnumCreationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Enums'));
    }

    public function test_enum_creation_default(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum', File::get($path));
    }

    public function test_enum_creation_with_backed_string(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '--backed' => 'string',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum: string', File::get($path));
    }

    public function test_enum_creation_with_backed_int(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '--backed' => 'int',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum: int', File::get($path));
    }

    public function test_enum_creation_cases(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '--cases' => 'First,Second,Third',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum', File::get($path));
        $this->assertStringContainsString('case First', File::get($path));
        $this->assertStringContainsString('case Second', File::get($path));
        $this->assertStringContainsString('case Third', File::get($path));
    }

    public function test_enum_creation_with_cases_and_backed_string(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '--backed' => 'string',
            '--cases' => 'First,Second,Third',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum: string', File::get($path));
        $this->assertStringContainsString("case First = 'first'", File::get($path));
        $this->assertStringContainsString("case Second = 'second'", File::get($path));
        $this->assertStringContainsString("case Third = 'third'", File::get($path));
    }

    public function test_enum_creation_with_cases_and_backed_int(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '--backed' => 'int',
            '--cases' => 'First,Second,Third',
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum: int', File::get($path));
        $this->assertStringContainsString('case First = 1', File::get($path));
        $this->assertStringContainsString('case Second = 2', File::get($path));
        $this->assertStringContainsString('case Third = 3', File::get($path));
    }

    public function test_enum_creation_with_label_method(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '-l' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum', File::get($path));
        $this->assertStringContainsString('public function label(): string', File::get($path));
    }

    public function test_enum_creation_with_trait(): void
    {
        $this->artisan('structura:enum', [
            'name' => 'SampleEnum',
            '-t' => true,
        ])
            ->assertExitCode(0);

        $path = app_path('Enums/SampleEnum.php');
        $this->assertTrue(File::exists($path));
        $this->assertStringContainsString('enum SampleEnum', File::get($path));
        $this->assertStringContainsString('use KaueF\Structura\Concerns\InteractsWithEnum;', File::get($path));
        $this->assertStringContainsString('use InteractsWithEnum', File::get($path));
    }
}
