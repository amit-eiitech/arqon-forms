<?php

namespace Arqon\Forms\Tests\Feature;

use Arqon\Forms\ArqonFormsServiceProvider;
use Arqon\Forms\Enums\FormStatus;
use Arqon\Forms\Enums\SubmissionStatus;
use Arqon\Forms\Filament\Actions\MarkSubmissionAsReadAction;
use Arqon\Forms\Models\Form;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;

class PublicPageRenderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            ArqonFormsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.key', 'base64:6Cu/hhUBOKBno3W161S124u9+tYf6n+o6n5Vv252T58=');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function test_public_page_renders_when_no_layout_is_configured(): void
    {
        $form = $this->createForm();

        $this->get('/forms/'.$form->slug)
            ->assertOk()
            ->assertSee('arqon-form-card')
            ->assertSee('arqon-label')
            ->assertSee('arqon-textarea')
            ->assertSee('.arqon-input', false)
            ->assertSee('.arqon-form-card', false)
            ->assertSee($form->name);
    }

    public function test_public_page_renders_inside_a_configured_layout(): void
    {
        $form = $this->createForm();

        $viewPath = sys_get_temp_dir().'/arqon-forms-test-views';
        if (! is_dir($viewPath)) {
            mkdir($viewPath, 0777, true);
        }
        file_put_contents($viewPath.'/arqon-test-layout.blade.php', '<html><body>LAYOUT-WRAPPER @yield("content")</body></html>');

        try {
            $this->app['view']->addLocation($viewPath);
            $this->app['config']->set('arqon-forms.render.layout', 'arqon-test-layout');

            $this->get('/forms/'.$form->slug)
                ->assertOk()
                ->assertSee('LAYOUT-WRAPPER')
                ->assertSee('.arqon-input', false);
        } finally {
            @unlink($viewPath.'/arqon-test-layout.blade.php');
        }
    }

    public function test_public_page_returns_not_found_for_inactive_form(): void
    {
        $form = $this->createForm(['status' => 'draft']);

        $this->get('/forms/'.$form->slug)->assertNotFound();
    }

    public function test_public_page_includes_form_component_styles(): void
    {
        $form = $this->createForm();

        $this->get('/forms/'.$form->slug)
            ->assertOk()
            ->assertSee('arqon-textarea')
            ->assertSee('.arqon-input', false);
    }

    public function test_form_status_is_cast_to_enum_and_draft_forms_are_not_public(): void
    {
        $form = $this->createForm(['status' => FormStatus::Draft->value]);

        self::assertSame(FormStatus::Draft, $form->status);

        $this->get('/forms/'.$form->slug)->assertNotFound();
    }

    public function test_blank_page_overrides_fall_back_to_form_name_and_description(): void
    {
        $form = $this->createForm([
            'description' => 'The form description',
            'settings' => [
                'page' => [
                    'header_title' => '',
                    'header_subtitle' => '  ',
                ],
            ],
        ]);

        $this->get('/forms/'.$form->slug)
            ->assertOk()
            ->assertSee($form->name)
            ->assertSee('The form description');
    }

    public function test_blank_submit_button_text_uses_default_label(): void
    {
        $form = $this->createForm([
            'settings' => [
                'submit_button' => '',
            ],
        ]);

        $this->get('/forms/'.$form->slug)
            ->assertOk()
            ->assertSee('>Submit</span>', false);
    }

    public function test_mark_as_read_action_updates_submission_status_and_handled_at(): void
    {
        $form = $this->createForm();
        $submission = $form->submissions()->create([
            'data' => [],
            'field_snapshot' => [],
            'status' => SubmissionStatus::New,
            'submitted_at' => now(),
        ]);
        $action = MarkSubmissionAsReadAction::make()->record($submission);

        self::assertTrue($action->isVisible());

        $handler = $action->getActionFunction();
        self::assertNotNull($handler);
        $handler($submission);

        $submission->refresh();
        self::assertSame(SubmissionStatus::Read, $submission->status);
        self::assertNotNull($submission->handled_at);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createForm(array $overrides = []): Form
    {
        return Form::create(array_merge([
            'name' => 'Feedback',
            'slug' => 'feedback',
            'status' => FormStatus::Published->value,
            'fields' => [
                ['name' => 'comments', 'type' => 'textarea', 'label' => 'Comments', 'required' => false],
            ],
            'settings' => [],
        ], $overrides));
    }
}
