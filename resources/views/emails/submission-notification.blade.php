A new submission was received for {{ $form->name }}.

@foreach (($submission->field_snapshot ?? []) as $field)
{{ $field['label'] ?? $field['name'] ?? 'Field' }}: @php($value = $submission->valueForField($field)){{ is_array($value) ? implode(', ', $value) : (string) ($value ?? '') }}
@endforeach
