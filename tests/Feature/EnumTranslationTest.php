<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Lang;
use KaueF\Structura\Attributes\Label;
use KaueF\Structura\Support\EnumSupport;
use Tests\TestCase;

enum TranslatedEnum: string
{
    #[Label('messages.active')]
    case Active = 'active';

    #[Label('Untranslated Label')]
    case Pending = 'pending';

    case Inactive = 'inactive';
}

class EnumTranslationTest extends TestCase
{
    public function test_it_translates_enum_labels_using_laravel_helper()
    {
        // Setup mock translation
        Lang::addLines([
            'messages.active' => 'Conta Ativa Traduzida',
        ], 'pt-BR');

        app()->setLocale('pt-BR');

        // Test with explicit translation key
        $this->assertEquals('Conta Ativa Traduzida', EnumSupport::label(TranslatedEnum::Active));

        // Test with untranslated string (returns itself)
        $this->assertEquals('Untranslated Label', EnumSupport::label(TranslatedEnum::Pending));

        // Test with fallback (name returns itself via __())
        $this->assertEquals('Inactive', EnumSupport::label(TranslatedEnum::Inactive));
    }
}
