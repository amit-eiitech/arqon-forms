<?php

namespace Arqon\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $table = 'arqon_form_submissions';

    protected $fillable = [
        'form_id',
        'data',
        'field_snapshot',
        'status',
        'ip_hash',
        'source_url',
        'submitted_at',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'field_snapshot' => 'array',
            'submitted_at' => 'datetime',
            'handled_at' => 'datetime',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function valueForField(array $field): mixed
    {
        $key = $field['name'] ?? null;
        if (! is_string($key) || $key === '') {
            return null;
        }

        $value = data_get($this->data, $key);
        $optionLabels = [];
        $options = $field['options'] ?? null;

        if (is_array($options)) {
            foreach ($options as $option) {
                if (is_array($option) && isset($option['value'])) {
                    $optionLabels[(string) $option['value']] = $option['label'] ?? $option['value'];
                }
            }
        }

        $labelFor = static fn(mixed $selected): mixed => (is_string($selected) || is_numeric($selected))
            ? ($optionLabels[(string) $selected] ?? $selected)
            : $selected;

        return is_array($value) ? array_map($labelFor, $value) : $labelFor($value);
    }
}
