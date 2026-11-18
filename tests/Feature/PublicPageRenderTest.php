<?php

namespace Arqon\Forms\Tests\Feature;

use Arqon\Forms\ArqonFormsServiceProvider;
use Arqon\Forms\Models\Form;
use Illuminate\Support\Facades\URL;
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

    public function test_preview_page_renders_when_no_layout_is_configured(): void
    {
        $form = $this->createForm();

        $url = URL::temporarySignedRoute('arqon-forms.preview', now()->addMinutes(30), ['form' => $form->getKey()]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Preview mode')
            ->assertSee($form->name);
    }

    public function test_preview_page_renders_inside_a_configured_layout(): void
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

            $url = URL::temporarySignedRoute('arqon-forms.preview', now()->addMinutes(30), ['form' => $form->getKey()]);

            $this->get($url)
                ->assertOk()
                ->assertSee('LAYOUT-WRAPPER')
                ->assertSee('Preview mode');
        } finally {
            @unlink($viewPath.'/arqon-test-layout.blade.php');
        }
    }

    public function test_public_page_returns_not_found_for_inactive_form(): void
    {
        $form = $this->createForm(['is_active' => false]);

        $this->get('/forms/'.$form->slug)->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createForm(array $overrides = []): Form
    {
        return Form::create(array_merge([
            'name' => 'Feedback',
            'slug' => 'feedback',
            'is_active' => true,
            'fields' => [
                ['name' => 'comments', 'type' => 'textarea', 'label' => 'Comments', 'required' => false],
            ],
            'settings' => [],
        ], $overrides));
    }
}
