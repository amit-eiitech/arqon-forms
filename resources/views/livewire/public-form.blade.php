<div>
    @if ($this->submitted)
        <div role="status" class="rounded-xl border border-green-200 bg-green-50 p-6 text-green-900 shadow-sm dark:border-green-800 dark:bg-green-950/50 dark:text-green-200">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-6 w-6 shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-base font-semibold">Submission Received</h3>
                    <p class="mt-1 text-sm text-green-800 dark:text-green-300">
                        {{ $form->setting('success_message', 'Thank you. Your response has been submitted.') }}
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
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $form->name }}</h1>
                @if ($form->description)
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $form->description }}</p>
                @endif
            </div>
        @endif

        <form wire:submit="submit" novalidate class="space-y-6">
            <div aria-hidden="true" class="hidden">
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
                        <label for="arqon-{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            {{ $label }}
                            @if ($required)
                                <span class="text-red-500" aria-hidden="true">*</span>
                            @endif
                        </label>

                        @if (!empty($field['help_text']))
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field['help_text'] }}</p>
                        @endif

                        <div class="mt-1.5">
                            @if ($type === 'textarea')
                                <textarea
                                    id="arqon-{{ $name }}"
                                    wire:model="answers.{{ $name }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    rows="4"
                                    @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                ></textarea>
                            @elseif ($type === 'select')
                                <select
                                    id="arqon-{{ $name }}"
                                    wire:model="answers.{{ $name }}"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                >
                                    <option value="">Choose an option</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? ($option['value'] ?? '') }}</option>
                                    @endforeach
                                </select>
                            @elseif (in_array($type, ['radio', 'checkbox']))
                                <div class="space-y-2.5">
                                    @foreach ($options as $index => $option)
                                        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-gray-700 dark:text-gray-200">
                                            <input
                                                type="{{ $type === 'radio' ? 'radio' : 'checkbox' }}"
                                                value="{{ $option['value'] ?? '' }}"
                                                wire:model="{{ $type === 'radio' ? 'answers.' . $name : 'answers.' . $name . '.' . $index }}"
                                                class="{{ $type === 'radio' ? 'rounded-full' : 'rounded' }} border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:checked:bg-primary-600"
                                            >
                                            <span>{{ $option['label'] ?? ($option['value'] ?? '') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($type === 'toggle')
                                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-gray-700 dark:text-gray-200">
                                    <input
                                        id="arqon-{{ $name }}"
                                        type="checkbox"
                                        wire:model="answers.{{ $name }}"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:checked:bg-primary-600"
                                    >
                                    <span>Yes</span>
                                </label>
                            @else
                                <input
                                    id="arqon-{{ $name }}"
                                    type="{{ in_array($type, ['text', 'email', 'number', 'tel', 'url', 'date']) ? $type : 'text' }}"
                                    wire:model="answers.{{ $name }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                >
                            @endif
                        </div>

                        @error('answers.' . $name)
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            @endforeach

            @error('form')
                <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror

            <div>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 disabled:opacity-50 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    <span wire:loading.remove>{{ $form->setting('submit_button', 'Submit') }}</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Submitting…
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
