<?php

namespace Arqon\Forms\Filament\Actions;

use Arqon\Forms\Enums\SubmissionStatus;
use Arqon\Forms\Models\FormSubmission;
use Filament\Actions\Action;

class MarkSubmissionAsReadAction
{
    public static function make(): Action
    {
        return Action::make('mark_as_read')
            ->label('Mark as read')
            ->icon('heroicon-o-check')
            ->color('gray')
            ->visible(fn(FormSubmission $record): bool => $record->status === SubmissionStatus::New)
            ->action(fn(FormSubmission $record) => $record->update([
                'status' => SubmissionStatus::Read,
                'handled_at' => now(),
            ]));
    }
}
