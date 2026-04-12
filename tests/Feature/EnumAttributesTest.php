<?php

namespace Tests\Feature;

use KaueF\Structura\Attributes\Color;
use KaueF\Structura\Attributes\DefaultCase;
use KaueF\Structura\Attributes\Icon;
use KaueF\Structura\Attributes\Label;
use KaueF\Structura\Support\EnumSupport;
use Tests\TestCase;

enum TestingAttributesEnum: string
{
    #[Label('Conta Ativa')]
    #[Color('success')]
    #[Icon('heroicon-o-check')]
    case Active = 'active';

    #[Label('Conta Pendente')]
    #[Color('warning')]
    case Pending = 'pending';

    #[DefaultCase]
    case Inactive = 'inactive';
}

class EnumAttributesTest extends TestCase
{
    public function test_can_extract_attributes_to_data()
    {
        $data = EnumSupport::toData(TestingAttributesEnum::class, color: true, icon: true);

        // Active case
        $this->assertEquals('active', $data[0]['id']);
        $this->assertEquals('Conta Ativa', $data[0]['name']);
        $this->assertEquals('success', $data[0]['color']);
        $this->assertEquals('heroicon-o-check', $data[0]['icon']);

        // Pending case - missing icon should be omitted due to minimalist toData
        array_multisort(array_column($data, 'id'), SORT_ASC, $data); // To ensure order by ID or Name

        $pending = array_values(array_filter($data, fn ($d) => $d['id'] === 'pending'))[0];
        $this->assertEquals('Conta Pendente', $pending['name']);
        $this->assertEquals('warning', $pending['color']);
        $this->assertArrayNotHasKey('icon', $pending);
    }

    public function test_try_from_default_returns_default_case_on_failure()
    {
        // Must return actual case if passed correctly
        $this->assertEquals(
            TestingAttributesEnum::Active,
            EnumSupport::tryFromDefault(TestingAttributesEnum::class, 'active')
        );

        // Must return default case if passed unknown value
        $this->assertEquals(
            TestingAttributesEnum::Inactive,
            EnumSupport::tryFromDefault(TestingAttributesEnum::class, 'nonsense_id')
        );
    }

    public function test_label_fallback_works()
    {
        // Inactive has no Label attribute, should fallback to name "Inactive"
        $this->assertEquals('Inactive', EnumSupport::label(TestingAttributesEnum::Inactive));
    }
}
