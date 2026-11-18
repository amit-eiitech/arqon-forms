<?php

namespace Arqon\Forms\Filament\Resources\Forms\Schemas;

use Arqon\Forms\Enums\FieldType;
use Arqon\Forms\Filament\Actions\PreviewFormAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Form configuration')
                    ->columnSpanFull()
                    ->vertical(false)
                    ->contained(true)
                    ->activeTab(1)
                    ->tabs([

                        /*
                             * GENERAL
                             */
                        Tab::make('General')
                            ->columns(2)
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Form name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->placeholder('e.g. Contact Form')
                                    ->helperText('An internal name to identify this form.')
                                    ->afterStateUpdated(
                                        fn($state, callable $set) =>
                                        $set('slug', str($state)->slug()->toString())
                                    ),

                                TextInput::make('slug')
                                    ->label('URL slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->alphaDash()
                                    ->maxLength(255)
                                    ->placeholder('contact-form')
                                    ->helperText('Used in the public URL of your form.'),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Briefly describe the purpose of this form.')
                                    ->helperText('An optional description to help administrators understand this form.'),

                                Toggle::make('is_active')
                                    ->label('Published')
                                    ->default(false)
                                    ->inline(false)
                                    ->helperText('When enabled, the form can accept public submissions.'),
                            ]),

                        /*
                             * FIELDS
                             */
                        Tab::make('Fields')
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Repeater::make('fields')
                                    ->label('Questions')
                                    ->required()
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->columnSpanFull()
                                    ->itemLabel(function (array $state): string {
                                        $label = filled($state['label'] ?? null)
                                            ? $state['label']
                                            : 'New question';

                                        $type = $state['type'] ?? 'Not configured';

                                        $required = ($state['required'] ?? false)
                                            ? ' • Required'
                                            : '';

                                        return "{$label} ({$type->getLabel()}{$required})";
                                    })
                                    ->addActionLabel('Add question')
                                    ->schema([
                                        Grid::make()
                                            ->columns(4)
                                            ->schema([
                                                Group::make()
                                                    ->columnSpan(3)
                                                    ->schema([
                                                        Hidden::make('name')
                                                            ->default(
                                                                fn(): string =>
                                                                'field_' . Str::lower((string) Str::ulid())
                                                            ),

                                                        TextInput::make('label')
                                                            ->label('Question / Label')
                                                            ->required()
                                                            ->maxLength(150)
                                                            ->placeholder('e.g. What is your email address?')
                                                            ->helperText('The question or label displayed above the input field.'),

                                                        Repeater::make('options')
                                                            ->label('Answer options')
                                                            ->table([
                                                                TableColumn::make('Option label'),
                                                            ])
                                                            ->schema([
                                                                TextInput::make('label')
                                                                    ->label('Option')
                                                                    ->required()
                                                                    ->hiddenLabel()
                                                                    ->placeholder('e.g. Other'),

                                                                Hidden::make('value')
                                                                    ->default(
                                                                        fn(): string =>
                                                                        'option_' . Str::lower((string) Str::ulid())
                                                                    ),
                                                            ])
                                                            ->defaultItems(1)
                                                            ->compact()
                                                            ->reorderable()
                                                            ->addActionLabel('Add option')
                                                            ->columnSpanFull()
                                                            ->visible(
                                                                fn(Get $get): bool =>
                                                                self::fieldTypeFromState($get('type'))?->hasOptions() ?? false
                                                            )
                                                            ->hint('Drag to change their order.'),

                                                        TextInput::make('default')
                                                            ->label('Default value')
                                                            ->placeholder('e.g. Handled by John Doe')
                                                            ->columnSpanFull()
                                                            ->visible(
                                                                fn(Get $get): bool =>
                                                                self::fieldTypeFromState($get('type')) === FieldType::Hidden
                                                            )
                                                            ->helperText('The value automatically assigned to this hidden field. Visitors will not see it.'),

                                                        TextInput::make('placeholder')
                                                            ->label('Placeholder')
                                                            ->maxLength(255)
                                                            ->columnSpanFull()
                                                            ->placeholder('e.g. name@example.com')
                                                            ->helperText('Optional example text displayed inside the input. It disappears when visitors start typing.')
                                                            ->visible(fn(Get $get): bool => (bool) $get('has_placeholder')),

                                                        TextInput::make('help_text')
                                                            ->label('Help text')
                                                            ->maxLength(255)
                                                            ->columnSpanFull()
                                                            ->placeholder('e.g. We will only use your email to contact you about your inquiry.')
                                                            ->helperText('Additional guidance displayed alongside the field to help visitors answer correctly.')
                                                            ->visible(fn(Get $get): bool => (bool) $get('has_help_text')),
                                                    ]),

                                                Group::make()
                                                    ->grow(false)
                                                    ->schema([
                                                        Select::make('type')
                                                            ->label('Field type')
                                                            ->required()
                                                            ->options(FieldType::class)
                                                            ->default(FieldType::Text)
                                                            ->selectablePlaceholder(false)
                                                            ->live()
                                                            ->helperText('Choose how visitors will provide their answer.'),

                                                        Toggle::make('required')
                                                            ->label('Required field')
                                                            ->default(false)
                                                            ->inline(true)
                                                            ->helperText('Visitors must complete this field before submitting.'),

                                                        Toggle::make('has_placeholder')
                                                            ->label('Add placeholder')
                                                            ->default(false)
                                                            ->inline(true)
                                                            ->live(),

                                                        Toggle::make('has_help_text')
                                                            ->label('Add help text')
                                                            ->default(false)
                                                            ->inline(true)
                                                            ->live(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        /*
                             * BEHAVIOUR
                             */
                        Tab::make('Behaviour')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->columns(2)
                            ->schema([
                                Fieldset::make('Submission experience')
                                    ->columns(1)
                                    ->schema([
                                        TextInput::make('settings.submit_button')
                                            ->label('Submit button text')
                                            ->default('Submit')
                                            ->maxLength(60)
                                            ->placeholder('Submit')
                                            ->helperText('The text displayed on the form submission button.'),

                                        Textarea::make('settings.success_message')
                                            ->label('Success message')
                                            ->default('Thank you. Your response has been submitted.')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->placeholder('Thank you for contacting us!')
                                            ->helperText('Displayed after a successful submission.'),

                                        TextInput::make('settings.redirect_url')
                                            ->label('Redirect URL')
                                            ->url()
                                            ->nullable()
                                            ->columnSpanFull()
                                            ->placeholder('https://example.com/thank-you')
                                            ->helperText('Optional. Redirect visitors to another page after submission. Leave empty to display the success message instead.'),
                                    ]),

                                Fieldset::make('Availability')
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('settings.availability.starts_at')
                                            ->label('Available from')
                                            ->seconds(false)
                                            ->nullable()
                                            ->helperText('The date and time when the form becomes available. Leave empty to make it available immediately.'),

                                        DateTimePicker::make('settings.availability.ends_at')
                                            ->label('Available until')
                                            ->seconds(false)
                                            ->nullable()
                                            ->helperText('The date and time when the form stops accepting submissions. Leave empty for no end date.'),

                                        TextInput::make('settings.availability.submission_limit')
                                            ->label('Maximum submissions')
                                            ->numeric()
                                            ->minValue(1)
                                            ->nullable()
                                            ->placeholder('e.g. 100')
                                            ->helperText('Maximum number of submissions allowed. Leave empty for unlimited submissions.'),
                                    ]),

                                Fieldset::make('Page layout')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('settings.page.show_header')
                                            ->label('Show page header')
                                            ->default((bool) config('arqon-forms.render.show_header', true))
                                            ->inline(false),

                                        Toggle::make('settings.page.show_footer')
                                            ->label('Show page footer')
                                            ->default((bool) config('arqon-forms.render.show_footer', false))
                                            ->inline(false),

                                        TextInput::make('settings.page.header_title')
                                            ->label('Header title override')
                                            ->placeholder('Leave blank to use form name'),

                                        TextInput::make('settings.page.header_subtitle')
                                            ->label('Header subtitle override')
                                            ->placeholder('Leave blank to use form description'),

                                        Textarea::make('settings.page.footer_text')
                                            ->label('Footer text')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->placeholder('Optional footer note for this form page'),
                                    ]),

                                Fieldset::make('Theme')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('settings.theme.primary_color')
                                            ->label('Primary color')
                                            ->placeholder((string) data_get(config('arqon-forms.render.theme'), 'primary_color', '#111827')),

                                        TextInput::make('settings.theme.background_color')
                                            ->label('Background color')
                                            ->placeholder((string) data_get(config('arqon-forms.render.theme'), 'background_color', '#f5f5f5')),

                                        TextInput::make('settings.theme.card_background_color')
                                            ->label('Card background color')
                                            ->placeholder((string) data_get(config('arqon-forms.render.theme'), 'card_background_color', '#ffffff')),

                                        TextInput::make('settings.theme.text_color')
                                            ->label('Text color')
                                            ->placeholder((string) data_get(config('arqon-forms.render.theme'), 'text_color', '#222222')),

                                        TextInput::make('settings.theme.radius')
                                            ->label('Card radius')
                                            ->placeholder((string) data_get(config('arqon-forms.render.theme'), 'radius', '12px')),
                                    ]),
                            ]),

                        /*
                             * NOTIFICATIONS
                             */
                        Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Toggle::make('settings.notification.enabled')
                                    ->label('Enable email notifications')
                                    ->default(false)
                                    ->inline(false)
                                    ->live()
                                    ->helperText('Send an email notification when a new response is received.'),

                                TagsInput::make('settings.notification.emails')
                                    ->label('Notification recipients')
                                    ->placeholder('Enter an email address')
                                    ->nestedRecursiveRules(['email'])
                                    ->helperText('Add one or more email addresses that should receive submission notifications.')
                                    ->visible(
                                        fn(Get $get): bool =>
                                        (bool) $get('settings.notification.enabled')
                                    ),
                            ]),
                    ]),
            ]);
    }

    private static function fieldTypeFromState(mixed $state): ?FieldType
    {
        if ($state instanceof FieldType) {
            return $state;
        }

        return is_string($state) ? FieldType::tryFrom($state) : null;
    }
}
