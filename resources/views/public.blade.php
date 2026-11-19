@extends($layout ?? 'arqon-forms::layouts.standalone')

@section($section ?? 'content')
    <style>
        {!! \Arqon\Forms\ArqonFormsServiceProvider::getDistCss() !!}
    </style>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($previewMode ?? false)
            <div class="arqon-preview-banner mb-6">
                Preview mode: this is a temporary signed preview page.
            </div>
        @endif

        <div class="arqon-form-card">
            @php($headerTitle = data_get($form->settings ?? [], 'page.header_title'))
            <header class="arqon-page-header mb-6">
                <h1 class="arqon-page-title">
                    {{ filled($headerTitle) ? $headerTitle : $form->name }}
                </h1>
                @php($subtitle = data_get($form->settings ?? [], 'page.header_subtitle'))
                @if (!filled($subtitle))
                    @php($subtitle = $form->description)
                @endif
                @if (filled($subtitle))
                    <p class="arqon-page-subtitle mt-2">{{ $subtitle }}</p>
                @endif
            </header>

            <livewire:arqon-forms.public-form :form-id="$form->id" :preview="$previewMode ?? false" :show-heading="false" />

            @php($footerText = data_get($form->settings ?? [], 'page.footer_text'))
            @if (!filled($footerText))
                @php($footerText = config('arqon-forms.render.footer_text'))
            @endif
            @if (filled($footerText))
                <footer class="arqon-page-footer mt-6">
                    {{ $footerText }}
                </footer>
            @endif
        </div>
    </div>
@endsection
