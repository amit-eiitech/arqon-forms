<?php

namespace Arqon\Forms\Filament\Resources\Submissions\Schemas;

use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Arqon\Forms\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class SubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('form.name')
                                    ->label('Form')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                            ]),
                        TextEntry::make('submitted_at')
                            ->label('Submitted At')
                            ->dateTime(),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                    ])
                    ->columnSpanFull(),

                Section::make('Responses')
                    ->schema(fn(FormSubmission $record) => collect($record->field_snapshot ?? [])
                        ->map(function (array $field) use ($record) {
                            return TextEntry::make('field_' . $field['name'])
                                ->label($field['label'] ?? $field['name'])
                                ->state(fn() => $record->valueForField($field))
                                ->listWithLineBreaks();
                        })
                        ->toArray())
                    ->columnSpanFull(),
            ]);
    }
}
