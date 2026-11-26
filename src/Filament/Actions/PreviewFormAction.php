<?php

namespace Arqon\Forms\Filament\Actions;

use Arqon\Forms\Models\Form as FormModel;
use Arqon\Forms\Traits\HasPackageIcons;
use Filament\Actions\Action;

class PreviewFormAction
{
    use HasPackageIcons;

    public static function make(): Action
    {
        return Action::make('preview')
            ->label('Preview')
            ->icon(static::packageIcon('preview'))
            ->color('gray')
            ->modalHeading(fn(FormModel $record): string => $record->name)
            ->modalDescription(fn(FormModel $record): ?string => $record->description)
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->modalCloseButton(true)
            ->modalCancelAction(false)
            ->modalContent(fn(FormModel $record) => view('arqon-forms::filament.preview-modal', [
                'form' => $record,
            ]))
            ->visible(fn(?FormModel $record): bool => $record !== null);
    }
}
