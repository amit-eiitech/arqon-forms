<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->name ?? config('app.name', 'Form') }}</title>
    @if (($previewMode ?? false) === true)
        <meta name="robots" content="noindex,nofollow">
    @endif
    @livewireStyles
</head>

<body class="min-h-full bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    @yield($section ?? 'content')
    @livewireScripts
</body>

</html>
