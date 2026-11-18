<?php

use Arqon\Forms\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('arqon-forms.route.middleware', ['web']))
    ->prefix(config('arqon-forms.route.prefix', 'forms'))
    ->name(config('arqon-forms.route.name_prefix', 'arqon-forms.'))
    ->group(function (): void {
        if (config('arqon-forms.preview.enabled', true)) {
            $previewPath = trim((string) config('arqon-forms.preview.path', '_preview'), '/');

            Route::get('/' . $previewPath . '/{form}', [PublicFormController::class, 'preview'])
                ->middleware('signed')
                ->whereNumber('form')
                ->name('preview');
        }

        Route::get('/{slug}', [PublicFormController::class, 'show'])->name('show');
    });
