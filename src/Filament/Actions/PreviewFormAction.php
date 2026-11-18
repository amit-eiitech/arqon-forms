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
            ->url(
                fn(FormModel $record): string => url(
                    trim(config('arqon-forms.route.prefix', 'forms'), '/') . '/' . $record->slug,
                ),
                shouldOpenInNewTab: true,
            )
            ->visible(fn(?FormModel $record): bool => $record !== null);
    }
}
