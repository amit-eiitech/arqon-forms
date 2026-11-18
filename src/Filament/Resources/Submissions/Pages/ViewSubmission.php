<?php

namespace Arqon\Forms\Filament\Resources\Submissions\Pages;

use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Arqon\Forms\Filament\Resources\Submissions\SubmissionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Arqon\Forms\Models\FormSubmission;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editForm')
                ->label('Edit form')
                ->icon('heroicon-o-pencil-square')
                ->url(fn(FormSubmission $record): string => FormResource::getUrl('edit', ['record' => $record->form_id]))
                ->visible(fn(FormSubmission $record): bool => $record->form_id !== null),
        ];
    }
}
