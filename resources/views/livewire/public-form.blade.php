<div>
    @if ($this->submitted)
        <div role="status" style="padding:1rem;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px">
            {{ $form->setting('success_message', 'Thank you. Your response has been submitted.') }}
            @if ($form->setting('redirect_url'))
                <script>window.location.href = @js($form->setting('redirect_url'));</script>
            @endif
        </div>
    @else
        <h1 style="font-size:1.75rem;margin:0 0 .5rem">{{ $form->name }}</h1>
        @if ($form->description)<p style="color:#555;margin:0 0 1.5rem">{{ $form->description }}</p>@endif

        <form wire:submit="submit" novalidate>
            <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden">
                <label>Leave this field empty<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
            </div>

            @foreach ($fields as $field)
                @php
                    $name = $field['name'] ?? 'field_'.$loop->index;
                    $type = $field['type'] ?? 'text';
                    $label = $field['label'] ?? $name;
                    $required = (bool) ($field['required'] ?? false);
                    $options = $field['options'] ?? [];
                    $fieldType = \Arqon\Forms\Enums\FieldType::tryFrom($type);
                    $maxLength = $fieldType ? $fieldType->maxLength() : null;
                @endphp
                @if ($type !== 'hidden')
                    <div style="margin-bottom:1.25rem">
                        <label for="arqon-{{ $name }}" style="display:block;font-weight:600;margin-bottom:.4rem">
                            {{ $label }} @if($required)<span aria-hidden="true" style="color:#b91c1c">*</span>@endif
                        </label>
                        @if (!empty($field['help_text']))<p style="font-size:.875rem;color:#666;margin:.2rem 0 .5rem">{{ $field['help_text'] }}</p>@endif

                        @if ($type === 'textarea')
                            <textarea id="arqon-{{ $name }}" wire:model="answers.{{ $name }}" placeholder="{{ $field['placeholder'] ?? '' }}" rows="4" @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif style="width:100%;box-sizing:border-box;padding:.7rem;border:1px solid #ccc;border-radius:6px"></textarea>
                        @elseif (in_array($type, ['select']))
                            <select id="arqon-{{ $name }}" wire:model="answers.{{ $name }}" style="width:100%;padding:.7rem;border:1px solid #ccc;border-radius:6px">
                                <option value="">Choose an option</option>
                                @foreach($options as $option)<option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? $option['value'] ?? '' }}</option>@endforeach
                            </select>
                        @elseif (in_array($type, ['radio', 'checkbox']))
                            <div style="display:grid;gap:.5rem">
                                @foreach($options as $option)
                                    <label style="font-weight:400;display:flex;gap:.5rem;align-items:center">
                                        <input type="{{ $type === 'radio' ? 'radio' : 'checkbox' }}" value="{{ $option['value'] ?? '' }}" wire:model="answers.{{ $name }}">
                                        {{ $option['label'] ?? $option['value'] ?? '' }}
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($type === 'toggle')
                            <label style="font-weight:400;display:flex;gap:.5rem;align-items:center"><input id="arqon-{{ $name }}" type="checkbox" wire:model="answers.{{ $name }}"> Yes</label>
                        @else
                            <input id="arqon-{{ $name }}" type="{{ in_array($type, ['text','email','number','tel','url','date']) ? $type : 'text' }}" wire:model="answers.{{ $name }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if ($maxLength !== null) maxlength="{{ $maxLength }}" @endif style="width:100%;box-sizing:border-box;padding:.7rem;border:1px solid #ccc;border-radius:6px">
                        @endif
                        @error('answers.'.$name)<p style="color:#b91c1c;font-size:.875rem;margin:.3rem 0">{{ $message }}</p>@enderror
                    </div>
                @endif
            @endforeach

            @error('form')<p role="alert" style="color:#b91c1c">{{ $message }}</p>@enderror
            <button type="submit" wire:loading.attr="disabled" style="background:#111827;color:white;padding:.75rem 1.25rem;border:0;border-radius:6px;cursor:pointer">
                <span wire:loading.remove>{{ $form->setting('submit_button', 'Submit') }}</span>
                <span wire:loading>Submitting…</span>
            </button>
        </form>
    @endif
</div>
