<?php

namespace Arqon\Forms\Filament\Resources\Submissions\Pages;

use Arqon\Forms\Filament\Actions\MarkSubmissionAsReadAction;
use Arqon\Forms\Filament\Actions\MarkSubmissionAsRepliedAction;
use Arqon\Forms\Filament\Resources\Submissions\SubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            MarkSubmissionAsReadAction::make(),
            MarkSubmissionAsRepliedAction::make(),
        ];
    }
}
