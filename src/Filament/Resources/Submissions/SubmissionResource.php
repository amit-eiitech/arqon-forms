<?php

namespace Arqon\Forms\Filament\Resources\Submissions;

use Arqon\Forms\Filament\Resources\Submissions\Pages\ListSubmissions;
use Arqon\Forms\Filament\Resources\Submissions\Pages\ViewSubmission;
use Arqon\Forms\Filament\Resources\Submissions\Schemas\SubmissionInfolist;
use Arqon\Forms\Filament\Resources\Submissions\Tables\SubmissionTable;
use Arqon\Forms\Models\FormSubmission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationGroup(): ?string
    {
        return config('arqon-forms.filament.navigation_group', 'Forms');
    }

    public static function getNavigationLabel(): string
    {
        return config('arqon-forms.filament.submissions_navigation_label', 'Submissions');
    }

    public static function table(Table $table): Table
    {
        return SubmissionTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubmissionInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'view' => ViewSubmission::route('/{record}'),
        ];
    }
}
