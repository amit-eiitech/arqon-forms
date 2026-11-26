<?php

namespace Arqon\Forms\Filament\Actions;

use Arqon\Forms\Enums\SubmissionStatus;
use Arqon\Forms\Models\FormSubmission;
use Arqon\Forms\Traits\HasPackageIcons;
use Filament\Actions\Action;

class MarkSubmissionAsRepliedAction
{
    use HasPackageIcons;

    public static function make(): Action
    {
        return Action::make('mark_as_replied')
            ->label('Mark as replied')
            ->icon(static::packageIcon('mark_as_replied'))
            ->color('gray')
            ->visible(fn(FormSubmission $record): bool => $record->status === SubmissionStatus::New ||  $record->status === SubmissionStatus::Read)
            ->action(fn(FormSubmission $record) => $record->update([
                'status' => SubmissionStatus::Replied,
                'handled_at' => now(),
            ]));
    }
}
