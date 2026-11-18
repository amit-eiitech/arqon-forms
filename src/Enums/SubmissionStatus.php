<?php

namespace Arqon\Forms\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Override;

enum SubmissionStatus: string implements HasLabel, HasColor
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Read => 'Read',
            self::Replied => 'Replied',
            self::Archived => 'Archived',
        };
    }

    #[Override]
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'info',
            self::Read => 'gray',
            self::Replied => 'success',
            self::Archived => 'warning',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->getLabel()])
            ->all();
    }
}
