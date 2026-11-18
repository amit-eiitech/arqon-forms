<?php

namespace Arqon\Forms\Services;

use Arqon\Forms\Enums\FieldType;
use Arqon\Forms\Models\Form;
use Arqon\Forms\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class FormSubmissionService
{
    /** @throws ValidationException */
    public function submit(Form $form, array $input, ?Request $request = null): FormSubmission
    {
        abort_unless($form->acceptsSubmissions(), 404);

        $definitions = collect($form->fields ?? [])
            ->filter(fn ($field) => is_array($field) && isset($field['name'], $field['type']) && is_string($field['type']) && FieldType::tryFrom($field['type']) !== null)
            ->values();

        $names = $definitions->pluck('name')->filter(fn ($name) => is_string($name) && preg_match('/^[a-zA-Z][a-zA-Z0-9_]{0,63}$/', $name));
        if ($names->count() !== $names->unique()->count() || $names->count() !== $definitions->count()) {
            throw ValidationException::withMessages(['form' => 'This form has an invalid field configuration. Please contact the site administrator.']);
        }

        $rules = [];
        $attributes = [];
        $allowed = [];

        foreach ($definitions as $field) {
            $name = (string) $field['name'];
            $type = FieldType::from($field['type']);
            if ($type === FieldType::Hidden) {
                continue;
            }

            $allowed[] = $name;
            $rules[$name] = $type->rules($field);
            $attributes[$name] = $field['label'] ?? $name;

            if (in_array($type, [FieldType::Select, FieldType::Radio], true)) {
                $values = collect($field['options'] ?? [])->pluck('value')->filter()->values()->all();
                $rules[$name][] = Rule::in($values);
            }

            if ($type === FieldType::Checkbox) {
                $values = collect($field['options'] ?? [])->pluck('value')->filter()->values()->all();
                $rules[$name.'.*'] = ['string', Rule::in($values)];
            }
        }

        $payload = collect($input)->only($allowed)->all();
        $validator = Validator::make($payload, $rules, [], $attributes);
        $validated = $validator->validate();

        $stored = [];
        foreach ($definitions as $field) {
            $name = (string) ($field['name'] ?? '');
            if (($field['type'] ?? null) === FieldType::Hidden->value) {
                $stored[$name] = $field['default'] ?? null;
            } elseif (array_key_exists($name, $validated)) {
                $stored[$name] = $validated[$name];
            }
        }

        $ipHash = null;
        if (config('arqon-forms.submission.store_ip_hash', true) && $request?->ip()) {
            $key = config('arqon-forms.submission.ip_hash_key') ?: (string) config('app.key');
            $ipHash = hash_hmac('sha256', $request->ip(), $key);
        }

        $submission = $form->submissions()->create([
            'data' => $stored,
            'field_snapshot' => $definitions->all(),
            'status' => 'new',
            'ip_hash' => $ipHash,
            'source_url' => $request?->headers->get('referer'),
            'submitted_at' => now(),
        ]);

        $this->sendNotifications($form, $submission);

        return $submission;
    }

    protected function sendNotifications(Form $form, FormSubmission $submission): void
    {
        $settings = $form->settings ?? [];
        $notification = data_get($settings, 'notification', []);
        if (! data_get($notification, 'enabled', false)) {
            return;
        }

        $emails = collect(data_get($notification, 'emails', []))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()->values()->all();

        if ($emails === []) {
            return;
        }

        $body = "A new submission was received for {$form->name}.\n\n";
        foreach ($submission->field_snapshot ?? [] as $field) {
            $label = $field['label'] ?? $field['name'] ?? 'Field';
            $value = $submission->valueForField($field);
            $body .= $label . ': ' . (is_array($value) ? implode(', ', $value) : (string) ($value ?? '')) . "\n";
        }

        $callback = fn () => Mail::raw($body, function ($message) use ($emails, $form): void {
            $message->to($emails)->subject('New form submission: '.$form->name);
        });

        try {
            $callback();
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
