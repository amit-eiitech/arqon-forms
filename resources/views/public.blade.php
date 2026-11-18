@extends($layout ?? 'arqon-forms::layouts.standalone')

@section($section ?? 'content')
    @if (filled($layout ?? null))
        <style>
            {!! \Arqon\Forms\ArqonFormsServiceProvider::getDistCss() !!}
        </style>
    @endif

    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($previewMode ?? false)
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                Preview mode: this is a temporary signed preview page.
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 dark:border-gray-800 dark:bg-gray-900">
            @if ($showHeader ?? true)
                <header class="mb-6 border-b border-gray-100 pb-5 dark:border-gray-800">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ data_get($form->settings ?? [], 'page.header_title', $form->name) }}
                    </h1>
                    @php($subtitle = data_get($form->settings ?? [], 'page.header_subtitle', $form->description))
                    @if (filled($subtitle))
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                    @endif
                </header>
            @endif

            <livewire:arqon-forms.public-form
                :form-id="$form->id"
                :preview="$previewMode ?? false"
                :show-heading="!($showHeader ?? true)"
            />

            @if ($showFooter ?? false)
                @php($footerText = data_get($form->settings ?? [], 'page.footer_text', config('arqon-forms.render.footer_text')))
                @if (filled($footerText))
                    <footer class="mt-6 border-t border-gray-100 pt-4 text-xs text-gray-400 dark:border-gray-800 dark:text-gray-500">
                        {{ $footerText }}
                    </footer>
                @endif
            @endif
        </div>
    </div>
@endsection
