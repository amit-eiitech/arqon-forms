<?php

namespace Arqon\Forms\Filament\Resources\Forms\Pages;

use Arqon\Forms\Filament\Actions\PreviewFormAction;
use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Arqon\Forms\Support\FormSettingsNormalizer;
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
        $data['settings'] = FormSettingsNormalizer::normalize($data['settings'] ?? []);

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
