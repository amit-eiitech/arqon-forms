<?php

namespace Arqon\Forms\Filament\Actions;

use Arqon\Forms\Models\Form as FormModel;
use Filament\Actions\Action;

class PreviewFormAction
{
    public static function make(): Action
    {
        return Action::make('preview')
            ->label('Preview')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn(FormModel $record): string => $record->name)
            ->modalDescription(fn(FormModel $record): ?string => $record->description)
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(fn(FormModel $record) => view('arqon-forms::filament.preview-modal', [
                'form' => $record,
            ]))
            ->visible(fn(?FormModel $record): bool => $record !== null);
    }
}
