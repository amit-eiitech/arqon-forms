<?php

namespace Arqon\Forms\Filament\Resources\Forms\Pages;

use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Arqon\Forms\Support\FormSettingsNormalizer;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fields'] = $this->normalizeFields($data['fields'] ?? []);
        $data['settings'] = FormSettingsNormalizer::normalize($data['settings'] ?? []);

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

}
