<?php

namespace Arqon\Forms\Filament\Resources\Forms\Pages;

use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fields'] = $this->normalizeFields($data['fields'] ?? []);
        $data['settings'] = $this->normalizeSettings($data['settings'] ?? []);

        return $data;
    }

    private function normalizeFields(array $fields): array
    {
        foreach ($fields as &$field) {
            $field['name'] = str($field['name'] ?? '')->snake()->toString();
            if (isset($field['options'])) {
                $field['options'] = array_values(array_filter($field['options'], fn ($option) => filled($option['value'] ?? null)));
            }
        }
        return array_values($fields);
    }

    private function normalizeSettings(array $settings): array
    {
        $rawEmails = data_get($settings, 'notification.emails', []);
        $emails = is_array($rawEmails) ? $rawEmails : preg_split('/[,;\s]+/', (string) $rawEmails, -1, PREG_SPLIT_NO_EMPTY);
        data_set($settings, 'notification.emails', $emails ?: []);
        return $settings;
    }
}
