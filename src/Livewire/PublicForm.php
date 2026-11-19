<?php

namespace Arqon\Forms\Livewire;

use Arqon\Forms\Models\Form;
use Arqon\Forms\Services\FormSubmissionService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PublicForm extends Component
{
    #[Locked]
    public int $formId;

    #[Locked]
    public bool $preview = false;

    public bool $showHeading = true;
    public array $answers = [];
    public string $website = '';
    public bool $submitted = false;

    public function mount(int $formId, bool $preview = false, bool $showHeading = true): void
    {
        $this->preview = $preview;
        $this->showHeading = $showHeading;

        $form = $preview
            ? Form::query()->findOrFail($formId)
            : Form::query()->published()->findOrFail($formId);

        if (! $preview) {
            abort_unless($form->acceptsSubmissions(), 404);
        }

        $this->formId = $form->id;
    }

    public function submit(FormSubmissionService $service): void
    {
        $form = $this->resolveFormForRendering();

        if ($this->website !== '') {
            $this->submitted = true;
            return;
        }

        if ($this->preview && ! config('arqon-forms.preview.allow_submissions', false)) {
            $this->addError('form', 'Preview mode is enabled. Submissions are disabled for preview links.');
            return;
        }

        $key = 'arqon-forms:' . $form->id . ':' . request()->ip();
        $max = (int) config('arqon-forms.submission.rate_limit.max_attempts', 5);
        $decay = (int) config('arqon-forms.submission.rate_limit.decay_minutes', 1);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $this->addError('form', 'Too many submissions. Please try again later.');
            return;
        }

        RateLimiter::hit($key, $decay * 60);

        try {
            $service->submit($form, $this->answers, request(), enforceAvailability: ! $this->preview);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError('answers.' . $field, $message);
                }
            }

            return;
        }

        $this->answers = [];
        $this->submitted = true;
        $this->dispatch('arqon-form-submitted');
    }

    public function render()
    {
        $form = $this->resolveFormForRendering();

        return view('arqon-forms::livewire.public-form', [
            'form' => $form,
            'fields' => $form->fields ?? [],
            'showHeading' => $this->showHeading,
            'preview' => $this->preview,
        ]);
    }

    protected function resolveFormForRendering(): Form
    {
        if ($this->preview) {
            return Form::query()->findOrFail($this->formId);
        }

        return Form::query()->published()->findOrFail($this->formId);
    }
}
