<?php

namespace Arqon\Forms\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum FieldType: string implements HasLabel, HasIcon, HasDescription
{
    private const TEXT_MAX_LENGTH = 500;
    private const TEXTAREA_MAX_LENGTH = 5000;

    case Text = 'text';
    case Textarea = 'textarea';
    case Email = 'email';
    case Number = 'number';
    case Tel = 'tel';
    case Url = 'url';
    case Select = 'select';
    case Radio = 'radio';
    case Checkbox = 'checkbox';
    case Date = 'date';
    case Toggle = 'toggle';
    case Hidden = 'hidden';

    #[Override]
    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Text => 'Short answer',
            self::Textarea => 'Paragraph',
            self::Email => 'Email',
            self::Number => 'Number',
            self::Tel => 'Phone',
            self::Url => 'Website URL',
            self::Select => 'Dropdown',
            self::Radio => 'Single choice',
            self::Checkbox => 'Multiple choice',
            self::Date => 'Date',
            self::Toggle => 'Yes / No',
            self::Hidden => 'Hidden value',
        };
    }

    #[Override]
    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Text => 'heroicon-arrow-down',
            self::Textarea => 'heroicon-arrow-down',
            self::Email => 'heroicon-arrow-down',
            self::Number => 'heroicon-arrow-down',
            self::Tel => 'heroicon-arrow-down',
            self::Url => 'heroicon-arrow-down',
            self::Select => 'heroicon-arrow-down',
            self::Radio => 'heroicon-arrow-down',
            self::Checkbox => 'heroicon-arrow-down',
            self::Date => 'heroicon-arrow-down',
            self::Toggle => 'heroicon-arrow-down',
            self::Hidden => 'heroicon-arrow-down',
        };
    }

    #[Override]
    public function getDescription(): string|Htmlable|null
    {
        return match ($this) {
            self::Text => 'A short, single-line answer. Maximum ' . number_format(self::TEXT_MAX_LENGTH) . ' characters.',
            self::Textarea => 'A long, multi-line answer. Maximum ' . number_format(self::TEXTAREA_MAX_LENGTH) . ' characters.',
            self::Email => 'Email address.',
            self::Number => 'Enter a numeric value.',
            self::Tel => 'Enter a phone number.',
            self::Url => 'Enter a valid website URL.',
            self::Select => 'Choose one option from a dropdown list.',
            self::Radio => 'Choose one option from a list.',
            self::Checkbox => 'Choose one or more options.',
            self::Date => 'Enter a valid date.',
            self::Toggle => 'Choose yes or no.',
            self::Hidden => 'Stored with the submission without showing an input to respondents.',
        };
    }

    public function maxLength(): ?int
    {
        return match ($this) {
            self::Text => self::TEXT_MAX_LENGTH,
            self::Textarea => self::TEXTAREA_MAX_LENGTH,
            default => null,
        };
    }

    public function hasOptions(): bool
    {
        return in_array($this, [self::Select, self::Radio, self::Checkbox], true);
    }

    public function rules(array $field): array
    {
        $rules = [];
        if ($this === self::Toggle && ($field['required'] ?? false)) {
            $rules[] = 'present';
        } elseif (($field['required'] ?? false) && $this !== self::Hidden) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $rules[] = match ($this) {
            self::Email => 'email',
            self::Number => 'numeric',
            self::Tel => 'string',
            self::Url => 'url',
            self::Date => 'date',
            self::Toggle => 'boolean',
            self::Checkbox => 'array',
            default => 'string',
        };

        if ($maxLength = $this->maxLength()) {
            $rules[] = 'max:' . $maxLength;
        }

        return $rules;
    }
}
