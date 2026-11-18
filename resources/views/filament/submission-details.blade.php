<div class="space-y-4">
    <div class="text-sm text-gray-500">{{ $submission->form?->name }} · {{ $submission->submitted_at?->toDayDateTimeString() }}</div>
    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach (($submission->field_snapshot ?? []) as $field)
            @php
                $key = $field['name'] ?? '';
                $value = $submission->valueForField($field);
            @endphp
            <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                <dt class="text-sm font-medium">{{ $field['label'] ?? $key }}</dt>
                <dd class="text-sm sm:col-span-2">{{ is_array($value) ? implode(', ', $value) : ($value ?? '—') }}</dd>
            </div>
        @endforeach
    </dl>
</div>
