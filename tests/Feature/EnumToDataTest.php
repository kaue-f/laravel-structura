<?php

namespace Tests\Feature;

use KaueF\Structura\Attributes\Color;
use KaueF\Structura\Attributes\Label;
use KaueF\Structura\Concerns\InteractsWithEnum;
use Tests\TestCase;

enum TestingToDataEnum: string
{
    use InteractsWithEnum;

    #[Label('Ativo')]
    #[Color('success')]
    case Active = 'active';

    case Inactive = 'inactive';
}

class EnumToDataTest extends TestCase
{
    public function test_todata_can_map_specific_fields()
    {
        // Passando map vazio, deve retornar apenas id e name
        $data = TestingToDataEnum::toData(map: []);

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayNotHasKey('color', $data[0]);
        $this->assertArrayNotHasKey('icon', $data[0]);

        $this->assertEquals('active', $data[0]['id']);
        $this->assertEquals('Ativo', $data[0]['name']);
    }

    public function test_todata_includes_only_id_and_name_by_default()
    {
        // Se map for null, agora deve vir apenas id e name (comportamento minimalista)
        $data = TestingToDataEnum::toData();

        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayNotHasKey('color', $data[0]);
        $this->assertArrayNotHasKey('icon', $data[0]);
    }

    public function test_todata_can_include_color_and_icon_via_boolean_flags()
    {
        // Por padrão não deve vir
        $data = TestingToDataEnum::toData();
        $this->assertArrayNotHasKey('color', $data[0]);

        // Habilitando via flag
        $data = TestingToDataEnum::toData(color: true);
        $this->assertArrayHasKey('color', $data[0]);
    }

    public function test_todata_can_rename_keys()
    {
        $data = TestingToDataEnum::toData(map: [
            'value' => 'id',
            'label' => 'name',
        ]);

        $this->assertArrayHasKey('value', $data[0]);
        $this->assertArrayHasKey('label', $data[0]);
        $this->assertArrayNotHasKey('id', $data[0]);
        $this->assertArrayNotHasKey('name', $data[0]);

        $this->assertEquals('active', $data[0]['value']);
        $this->assertEquals('Ativo', $data[0]['label']);
    }

    public function test_todata_can_use_closures_and_static_values()
    {
        $data = TestingToDataEnum::toData(map: [
            'id',
            'custom' => fn ($case) => 'Prefix: '.$case->name,
            'fixed' => 'Static Value',
        ]);

        $this->assertEquals('active', $data[0]['id']);
        $this->assertEquals('Prefix: Active', $data[0]['custom']);
        $this->assertEquals('Static Value', $data[0]['fixed']);
    }
}
