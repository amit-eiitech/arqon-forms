<?php

namespace Arqon\Forms\Models;

use Arqon\Forms\Enums\FormStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $table = 'arqon_forms';

    protected $fillable = ['name', 'slug', 'description', 'fields', 'settings', 'status'];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'settings' => 'array',
            'status' => FormStatus::class,
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', FormStatus::Published);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }

    public function acceptsSubmissions(): bool
    {
        if ($this->status !== FormStatus::Published) {
            return false;
        }

        $startsAt = $this->setting('availability.starts_at');
        $endsAt = $this->setting('availability.ends_at');
        $limit = $this->setting('availability.submission_limit');

        if ($startsAt && now()->lt($startsAt)) {
            return false;
        }

        if ($endsAt && now()->gt($endsAt)) {
            return false;
        }

        if ($limit && $this->submissions()->count() >= (int) $limit) {
            return false;
        }

        return true;
    }
}
