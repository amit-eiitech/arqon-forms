<?php

namespace Arqon\Forms\Http\Controllers;

use Arqon\Forms\Models\Form;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PublicFormController extends Controller
{
    public function show(string $slug): View
    {
        $form = Form::query()->active()->where('slug', $slug)->firstOrFail();

        abort_unless($form->acceptsSubmissions(), 404);

        return view('arqon-forms::public', compact('form'));
    }
}
