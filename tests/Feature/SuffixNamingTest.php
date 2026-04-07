<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SuffixNamingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path());
    }

    public function test_action_suffix_is_appended(): void
    {
        $this->artisan('structura:action', ['name' => 'Logout'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Actions/LogoutAction.php')));

        $this->artisan('structura:action', ['name' => 'LoginAction'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Actions/LoginAction.php')));
    }

    public function test_cache_suffix_is_appended(): void
    {
        $this->artisan('structura:cache', ['name' => 'User'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Caches/UserCache.php')));
    }

    public function test_dto_suffix_is_appended_and_forced_to_uppercase(): void
    {
        $this->artisan('structura:dto', ['name' => 'User'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('DTOs/UserDTO.php')));

        $this->artisan('structura:dto', ['name' => 'ProductDto'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('DTOs/ProductDTO.php')));
    }

    public function test_enum_suffix_is_appended(): void
    {
        $this->artisan('structura:enum', ['name' => 'Status'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Enums/StatusEnum.php')));
    }

    public function test_helper_suffix_is_appended(): void
    {
        $this->artisan('structura:helper', ['name' => 'String'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Helpers/StringHelper.php')));
    }

    public function test_service_suffix_is_appended(): void
    {
        $this->artisan('structura:service', ['name' => 'Comment'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Services/CommentService.php')));
    }

    public function test_trait_suffix_is_not_appended_automatically(): void
    {
        $this->artisan('structura:trait', ['name' => 'Loggable'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Concerns/Loggable.php')));

        $this->artisan('structura:trait', ['name' => 'AuthenticatableTrait'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Concerns/AuthenticatableTrait.php')));
    }

    public function test_custom_suffix_from_config(): void
    {
        config(['structura.suffixes.service' => 'Srv']);

        $this->artisan('structura:service', ['name' => 'Payment'])->assertExitCode(0);
        $this->assertTrue(File::exists(app_path('Services/PaymentSrv.php')));
    }
}
