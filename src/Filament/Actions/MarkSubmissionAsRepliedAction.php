<?php

namespace Arqon\Forms\Filament\Actions;

use Arqon\Forms\Enums\SubmissionStatus;
use Arqon\Forms\Models\FormSubmission;
use Filament\Actions\Action;

class MarkSubmissionAsRepliedAction
{
    public static function make(): Action
    {
        return Action::make('mark_as_replied')
            ->label('Mark as replied')
            ->icon('heroicon-o-arrow-up-right')
            ->color('gray')
            ->visible(fn(FormSubmission $record): bool => $record->status === SubmissionStatus::New ||  $record->status === SubmissionStatus::Read)
            ->action(fn(FormSubmission $record) => $record->update([
                'status' => SubmissionStatus::Replied,
                'handled_at' => now(),
            ]));
    }
}
