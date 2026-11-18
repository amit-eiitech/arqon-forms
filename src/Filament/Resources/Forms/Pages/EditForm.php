<?php

namespace Arqon\Forms\Filament\Resources\Forms\Pages;

use Arqon\Forms\Filament\Actions\PreviewFormAction;
use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach ($data['fields'] ?? [] as &$field) {
            $field['name'] = str($field['name'] ?? '')->snake()->toString();
            if (isset($field['options'])) {
                $field['options'] = array_values(array_filter($field['options'], fn($option) => filled($option['value'] ?? null)));
            }
        }
        $data['fields'] = array_values($data['fields'] ?? []);
        $rawEmails = data_get($data, 'settings.notification.emails', []);
        $emails = is_array($rawEmails) ? $rawEmails : preg_split('/[,;\s]+/', (string) $rawEmails, -1, PREG_SPLIT_NO_EMPTY);
        data_set($data, 'settings.notification.emails', $emails ?: []);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            PreviewFormAction::make(),
            DeleteAction::make()
        ];
    }
}
