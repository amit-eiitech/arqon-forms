<?php

namespace Arqon\Forms;

use Arqon\Forms\Livewire\PublicForm;
use Arqon\Forms\Services\FormSubmissionService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ArqonFormsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/arqon-forms.php', 'arqon-forms');

        $this->app->singleton(FormSubmissionService::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/arqon-forms.php' => config_path('arqon-forms.php'),
        ], 'arqon-forms-config');

        $this->publishes([
            __DIR__.'/../resources/views/livewire/public-form.blade.php' => resource_path('views/vendor/arqon-forms/livewire/public-form.blade.php'),
            __DIR__.'/../resources/views/public.blade.php' => resource_path('views/vendor/arqon-forms/public.blade.php'),
            __DIR__.'/../resources/views/emails/submission-notification.blade.php' => resource_path('views/vendor/arqon-forms/emails/submission-notification.blade.php'),
        ], 'arqon-forms-views');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'arqon-forms');

        Blade::component('arqon-form', \Arqon\Forms\View\Components\ArqonForm::class);

        if (class_exists(Livewire::class)) {
            Livewire::component('arqon-forms.public-form', PublicForm::class);
        }

        if (config('arqon-forms.route.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }

    public static function getDistCss(): string
    {
        $path = __DIR__.'/../dist/arqon-forms.css';

        if (file_exists($path)) {
            return (string) file_get_contents($path);
        }

        return '';
    }
}
