<?php

namespace Arqon\Forms\Filament\Resources\Forms\Tables;

use Arqon\Forms\Filament\Actions\PreviewFormAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FormTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->copyable(),

                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean()
                    ->alignCenter(),

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
                TernaryFilter::make('is_active')
                    ->label('Published')
            ])
            ->recordActions([
                PreviewFormAction::make(),

                ActionGroup::make([
                    EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
