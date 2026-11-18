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
            __DIR__.'/../resources/views' => resource_path('views/vendor/arqon-forms'),
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
}
