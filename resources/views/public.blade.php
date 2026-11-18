<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->name }}</title>
    @livewireStyles
</head>
<body style="font-family:system-ui,sans-serif;background:#f5f5f5;margin:0;padding:2rem 1rem;color:#222">
    <main style="max-width:720px;margin:0 auto;background:white;padding:2rem;border-radius:12px">
        <livewire:arqon-forms.public-form :form-id="$form->id" />
    </main>
    @livewireScripts
</body>
</html>
