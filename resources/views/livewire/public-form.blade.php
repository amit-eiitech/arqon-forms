<div>
    @if ($this->submitted)
        @php
            $successMessage = $form->setting('success_message');
        @endphp
        <div role="status" class="arqon-success-message">
            <div class="arqon-success-content">
                <svg class="arqon-success-icon mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="arqon-success-title">Submission Received</h3>
                    <p class="arqon-success-text mt-1">
                        {{ filled($successMessage) ? $successMessage : 'Thank you. Your response has been submitted.' }}
                    </p>
                </div>
            </div>
            @if ($form->setting('redirect_url'))
                <script>
                    window.location.href = @js($form->setting('redirect_url'));
                </script>
            @endif
        </div>
    @else
        @if ($showHeading)
            <div class="mb-6">
                <h1 class="arqon-form-title">{{ $form->name }}</h1>
                @if ($form->description)
                    <p class="arqon-form-description mt-1.5">{{ $form->description }}</p>
                @endif
            </div>
        @endif

        <form wire:submit="submit" novalidate class="arqon-form">
            <div aria-hidden="true" class="arqon-honeypot">
                <label>Leave this field empty<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
            </div>

            @foreach ($fields as $field)
                @php
                    $name = $field['name'] ?? 'field_' . $loop->index;
                    $type = $field['type'] ?? 'text';
                    $label = $field['label'] ?? $name;
                    $required = (bool) ($field['required'] ?? false);
                    $options = $field['options'] ?? [];
                    $fieldType = \Arqon\Forms\Enums\FieldType::tryFrom($type);
                    $maxLength = $fieldType ? $fieldType->maxLength() : null;
                @endphp

                @if ($type !== 'hidden')
                    <div>
                        <label for="arqon-{{ $name }}" class="arqon-label">
                            {{ $label }}
                            @if ($required)
                                <span class="arqon-required" aria-hidden="true">*</span>
                            @endif
                        </label>

                        @if (!empty($field['help_text']))
                            <p class="arqon-help-text">{{ $field['help_text'] }}</p>
                        @endif

                        <div class="mt-1.5">
                            @if ($type === 'textarea')
                                <textarea id="arqon-{{ $name }}" wire:model="answers.{{ $name }}" placeholder="{{ $field['placeholder'] ?? '' }}" rows="4" @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif class="arqon-textarea"></textarea>
                            @elseif ($type === 'select')
                                <select id="arqon-{{ $name }}" wire:model="answers.{{ $name }}" class="arqon-select">
                                    <option value="">Choose an option</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? ($option['value'] ?? '') }}</option>
                                    @endforeach
                                </select>
                            @elseif (in_array($type, ['radio', 'checkbox']))
                                <div class="arqon-choice-list">
                                    @foreach ($options as $index => $option)
                                        <label class="arqon-choice-label">
                                            <input type="{{ $type === 'radio' ? 'radio' : 'checkbox' }}" value="{{ $option['value'] ?? '' }}" wire:model="{{ $type === 'radio' ? 'answers.' . $name : 'answers.' . $name . '.' . $index }}" class="{{ $type === 'radio' ? 'arqon-radio' : 'arqon-checkbox' }}">
                                            <span>{{ $option['label'] ?? ($option['value'] ?? '') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($type === 'toggle')
                                <label class="arqon-choice-label">
                                    <input id="arqon-{{ $name }}" type="checkbox" wire:model="answers.{{ $name }}" class="arqon-checkbox">
                                    <span>Yes</span>
                                </label>
                            @else
                                <input id="arqon-{{ $name }}" type="{{ in_array($type, ['text', 'email', 'number', 'tel', 'url', 'date']) ? $type : 'text' }}" wire:model="answers.{{ $name }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif class="arqon-input">
                            @endif
                        </div>

                        @error('answers.' . $name)
                            <p class="arqon-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            @endforeach

            @error('form')
                <div role="alert" class="arqon-form-error">
                    {{ $message }}
                </div>
            @enderror

            <div>
                @php
                    $submitButtonText = $form->setting('submit_button');
                @endphp
                <button type="submit" wire:loading.attr="disabled" @if($preview) disabled title="Submissions are disabled in preview" @endif class="arqon-submit-button @if($preview) opacity-60 cursor-not-allowed @endif">
                    <span wire:loading.remove>{{ filled($submitButtonText) ? $submitButtonText : 'Submit' }}</span>
                    <span wire:loading class="arqon-submit-loading">
                        <svg class="arqon-submit-spinner" viewBox="0 0 24 24" fill="none">
                            <circle class="arqon-spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="arqon-spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Submitting…
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
