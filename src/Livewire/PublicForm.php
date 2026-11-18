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

    public array $answers = [];
    public string $website = '';
    public bool $submitted = false;

    public function mount(int $formId): void
    {
        $form = Form::query()->active()->findOrFail($formId);
        abort_unless($form->acceptsSubmissions(), 404);
        $this->formId = $form->id;
    }

    public function submit(FormSubmissionService $service): void
    {
        $form = Form::query()->active()->findOrFail($this->formId);

        if ($this->website !== '') {
            $this->submitted = true;
            return;
        }

        $key = 'arqon-forms:'.$form->id.':'.request()->ip();
        $max = (int) config('arqon-forms.submission.rate_limit.max_attempts', 5);
        $decay = (int) config('arqon-forms.submission.rate_limit.decay_minutes', 1);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $this->addError('form', 'Too many submissions. Please try again later.');
            return;
        }

        RateLimiter::hit($key, $decay * 60);

        try {
            $service->submit($form, $this->answers, request());
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError('answers.'.$field, $message);
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
        $form = Form::query()->active()->findOrFail($this->formId);

        return view('arqon-forms::livewire.public-form', [
            'form' => $form,
            'fields' => $form->fields ?? [],
        ]);
    }
}
