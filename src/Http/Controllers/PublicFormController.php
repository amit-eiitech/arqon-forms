<?php

namespace Arqon\Forms\Http\Controllers;

use Arqon\Forms\Models\Form;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PublicFormController extends Controller
{
    public function show(string $slug): View
    {
        $form = Form::query()->published()->where('slug', $slug)->firstOrFail();

        abort_unless($form->acceptsSubmissions(), 404);

        $configuredLayout = config('arqon-forms.render.layout');
        $layout = is_string($configuredLayout) && $configuredLayout !== '' && view()->exists($configuredLayout)
            ? $configuredLayout
            : null;

        $section = config('arqon-forms.render.section', 'content');
        $section = is_string($section) && $section !== '' ? $section : 'content';

        return view('arqon-forms::public', [
            'form' => $form,
            'layout' => $layout,
            'section' => $section,
        ]);
    }
}
