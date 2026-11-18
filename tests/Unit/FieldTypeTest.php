<?php

namespace Arqon\Forms\Tests\Unit;

use Arqon\Forms\Enums\FieldType;
use PHPUnit\Framework\TestCase;

class FieldTypeTest extends TestCase
{
    public function test_field_types_have_labels_and_validation_rules(): void
    {
        self::assertSame('Short answer', FieldType::Text->getLabel());
        self::assertContains('required', FieldType::Email->rules(['required' => true]));
        self::assertContains('email', FieldType::Email->rules(['required' => true]));
    }

    public function test_field_types_have_descriptions(): void
    {
        self::assertSame(
            'A short, single-line answer. Maximum 500 characters.',
            FieldType::Text->getDescription(),
        );
        self::assertSame(
            'A long, multi-line answer. Maximum 5,000 characters.',
            FieldType::Textarea->getDescription(),
        );

        foreach (FieldType::cases() as $fieldType) {
            self::assertNotEmpty($fieldType->getDescription());
        }
    }

    public function test_text_types_use_fixed_maximum_lengths(): void
    {
        self::assertSame(500, FieldType::Text->maxLength());
        self::assertSame(5000, FieldType::Textarea->maxLength());
        self::assertContains('max:500', FieldType::Text->rules(['validation' => ['max' => 10]]));
        self::assertContains('max:5000', FieldType::Textarea->rules([]));
        self::assertNotContains('max:10', FieldType::Text->rules(['validation' => ['max' => 10]]));
        self::assertNull(FieldType::Number->maxLength());
    }

    public function test_only_choice_field_types_have_options(): void
    {
        foreach ([FieldType::Select, FieldType::Radio, FieldType::Checkbox] as $fieldType) {
            self::assertTrue($fieldType->hasOptions());
        }

        self::assertFalse(FieldType::Text->hasOptions());
        self::assertFalse(FieldType::Textarea->hasOptions());
    }
}
