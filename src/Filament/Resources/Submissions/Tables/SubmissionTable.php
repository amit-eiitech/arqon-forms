<?php

namespace Arqon\Forms\Filament\Resources\Submissions\Tables;

use Arqon\Forms\Enums\SubmissionStatus;
use Arqon\Forms\Filament\Actions\MarkSubmissionAsReadAction;
use Arqon\Forms\Filament\Actions\MarkSubmissionAsRepliedAction;
use Arqon\Forms\Models\FormSubmission;
use Arqon\Forms\Traits\HasPackageIcons;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionTable
{
    use HasPackageIcons;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SubmissionStatus::options()),

                SelectFilter::make('form_id')
                    ->label('Form')
                    ->relationship('form', 'name'),
            ])
            ->recordActions([
                MarkSubmissionAsReadAction::make(),
                MarkSubmissionAsRepliedAction::make(),
                ActionGroup::make([
                    ActionGroup::make([
                        ViewAction::make()
                            ->label('View submission')
                            ->icon(static::packageIcon('view')),

                        Action::make('archive')
                            ->label('Archive')
                            ->icon(static::packageIcon('archive'))
                            ->visible(fn(FormSubmission $record) => $record->status !== SubmissionStatus::Archived)
                            ->action(fn(FormSubmission $record) => $record->update(['status' => SubmissionStatus::Archived->value, 'handled_at' => now()])),
                    ])->dropdown(false),

                    DeleteAction::make()
                        ->icon(static::packageIcon('delete')),
                ]),
            ])
            ->headerActions([
                Action::make('exportCsv')->label('Export CSV')->icon(static::packageIcon('export_csv'))
                    ->action(fn(): StreamedResponse => static::exportCsv()),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    // Export to CSV
    protected static function exportCsv(): StreamedResponse
    {
        $records = FormSubmission::query()->with('form')->latest('submitted_at')->get();
        $fieldMap = [];

        foreach ($records as $record) {
            foreach ($record->field_snapshot ?? [] as $field) {
                $key = $field['name'] ?? null;
                if ($key) {
                    $fieldMap[$key] = $field['label'] ?? $key;
                }
            }
        }

        return response()->streamDownload(function () use ($records, $fieldMap): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_merge(['Submission ID', 'Form', 'Status', 'Submitted At'], array_values($fieldMap)));

            foreach ($records as $record) {
                $row = [$record->id, $record->form?->name, $record->status, $record->submitted_at?->toDateTimeString()];
                $fieldsByKey = collect($record->field_snapshot ?? [])->keyBy('name');

                foreach (array_keys($fieldMap) as $key) {
                    $field = $fieldsByKey->get($key, ['name' => $key]);
                    $value = $record->valueForField($field) ?? '';
                    $value = is_array($value) ? implode(', ', $value) : (string) $value;
                    if (preg_match('/^[=+\-@\t\r]/', $value)) {
                        $value = "'" . $value;
                    }
                    $row[] = $value;
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'arqon-form-submissions-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
