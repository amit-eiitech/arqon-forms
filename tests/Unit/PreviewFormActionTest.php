<?php

namespace Arqon\Forms\Tests\Unit;

use Arqon\Forms\ArqonFormsServiceProvider;
use Arqon\Forms\Filament\Actions\PreviewFormAction;
use Arqon\Forms\Models\Form;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;

class PreviewFormActionTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            ArqonFormsServiceProvider::class,
        ];
    }

    public function test_preview_action_is_configured_as_modal(): void
    {
        $action = PreviewFormAction::make();

        self::assertInstanceOf(Action::class, $action);
        self::assertSame('preview', $action->getName());
        self::assertSame('Preview', $action->getLabel());
        self::assertEquals(Heroicon::OutlinedEye, $action->getIcon());
        self::assertNull($action->getModalCancelAction());
        self::assertTrue($action->hasModalCloseButton());
        self::assertNull($action->getModalSubmitAction());
    }

    public function test_preview_modal_content_renders_view(): void
    {
        $form = new Form([
            'name' => 'Survey',
            'slug' => 'survey',
            'fields' => [],
        ]);
        $form->id = 1;

        $action = PreviewFormAction::make()->record($form);
        $modalContent = $action->getModalContent();

        self::assertNotNull($modalContent);
        self::assertSame('arqon-forms::filament.preview-modal', $modalContent->name());
        self::assertSame($form, $modalContent->getData()['form']);
    }
}
