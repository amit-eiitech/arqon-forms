<?php

namespace Arqon\Forms\Filament\Resources\Forms\Tables;

use Arqon\Forms\Enums\FormStatus;
use Arqon\Forms\Models\Form;
use Arqon\Forms\Traits\HasPackageIcons;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormTable
{
    use HasPackageIcons;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(FormStatus::class)
            ])
            ->recordActions([
                Action::make('open_public_form')
                    ->label('Go to published page')
                    ->icon(static::packageIcon('open_public_form'))
                    ->url(fn(Form $record): string => route(
                        config('arqon-forms.route.name_prefix', 'arqon-forms.') . 'show',
                        ['slug' => $record->slug],
                    ))
                    ->openUrlInNewTab()
                    ->visible(fn(Form $record): bool => $record->status === FormStatus::Published),

                ActionGroup::make([
                    EditAction::make()
                        ->icon(static::packageIcon('edit')),
                    DeleteAction::make()
                        ->icon(static::packageIcon('delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
