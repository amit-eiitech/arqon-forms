<?php

namespace Arqon\Forms\Filament;

use Arqon\Forms\Filament\Resources\Forms\FormResource;
use Arqon\Forms\Filament\Resources\Submissions\SubmissionResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ArqonFormsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'arqon-forms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            FormResource::class,
            SubmissionResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
