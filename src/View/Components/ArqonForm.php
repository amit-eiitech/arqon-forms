<?php

namespace Arqon\Forms\View\Components;

use Arqon\Forms\Models\Form;
use Closure;
use Illuminate\View\Component;

class ArqonForm extends Component
{
    public function __construct(public string $slug) {}

    public function render(): Closure
    {
        $form = Form::query()->active()->where('slug', $this->slug)->firstOrFail();
        abort_unless($form->acceptsSubmissions(), 404);

        return fn () => '<livewire:arqon-forms.public-form :form-id="' . $form->id . '" />';
    }
}
