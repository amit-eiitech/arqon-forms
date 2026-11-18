<?php

namespace Arqon\Forms\Filament\Resources\Forms;

use Arqon\Forms\Enums\FieldType;
use Arqon\Forms\Filament\Resources\Forms\Pages\CreateForm;
use Arqon\Forms\Filament\Resources\Forms\Pages\EditForm;
use Arqon\Forms\Filament\Resources\Forms\Pages\ListForms;
use Arqon\Forms\Filament\Resources\Forms\Schemas\FormForm;
use Arqon\Forms\Filament\Resources\Forms\Tables\FormTable;
use Arqon\Forms\Models\Form as FormModel;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return config('arqon-forms.filament.navigation_group', 'Forms');
    }

    public static function getNavigationLabel(): string
    {
        return config('arqon-forms.filament.forms_navigation_label', 'Forms');
    }

    public static function form(Schema $schema): Schema
    {
        return FormForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }
}
