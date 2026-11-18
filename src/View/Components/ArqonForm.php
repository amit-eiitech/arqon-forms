<?php

namespace Arqon\Forms\View\Components;

use Arqon\Forms\Models\Form;
use Illuminate\View\Component;
use Illuminate\View\View;

class ArqonForm extends Component
{
    public function __construct(public string $slug) {}

    public function render(): View
    {
        $form = Form::query()->active()->where('slug', $this->slug)->firstOrFail();
        abort_unless($form->acceptsSubmissions(), 404);

        return view('arqon-forms::components.form', compact('form'));
    }
}
