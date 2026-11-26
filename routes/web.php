<?php

use Arqon\Forms\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('arqon-forms.route.middleware', ['web']))
    ->prefix(config('arqon-forms.route.prefix', 'forms'))
    ->name(config('arqon-forms.route.name_prefix', 'arqon-forms.'))
    ->group(function (): void {
        Route::get('/{slug}', [PublicFormController::class, 'show'])->name('show');
    });
