<?php

namespace Tests\Unit;

use KaueF\Structura\Attributes\Ignore;
use KaueF\Structura\Support\EnumSupport;
use Tests\TestCase;

enum SampleStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    #[Ignore]
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::Archived => 'Arquivado',
        };
    }
}

class EnumSupportIgnoreTest extends TestCase
{
    public function test_ignore_attribute_excludes_case_from_values()
    {
        $values = EnumSupport::values(SampleStatusEnum::class);
        $this->assertCount(2, $values);
        $this->assertContains('active', $values);
        $this->assertContains('inactive', $values);
        $this->assertNotContains('archived', $values);
    }

    public function test_ignore_attribute_excludes_case_from_names()
    {
        $names = EnumSupport::names(SampleStatusEnum::class);
        $this->assertCount(2, $names);
        $this->assertContains('Active', $names);
        $this->assertContains('Inactive', $names);
        $this->assertNotContains('Archived', $names);
    }

    public function test_ignore_attribute_excludes_case_from_labels()
    {
        $labels = EnumSupport::labels(SampleStatusEnum::class);
        $this->assertCount(2, $labels);
        $this->assertContains('Ativo', $labels);
        $this->assertContains('Inativo', $labels);
        $this->assertNotContains('Arquivado', $labels);
    }

    public function test_ignore_attribute_excludes_case_from_to_array()
    {
        $array = EnumSupport::toArray(SampleStatusEnum::class);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('active', $array);
        $this->assertArrayHasKey('inactive', $array);
        $this->assertArrayNotHasKey('archived', $array);
    }

    public function test_ignore_attribute_excludes_case_from_to_data()
    {
        $data = EnumSupport::toData(SampleStatusEnum::class);
        $this->assertCount(2, $data);
        $ids = array_column($data, 'id');
        $this->assertContains('active', $ids);
        $this->assertNotContains('archived', $ids);
    }
}
