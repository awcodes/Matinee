<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }}</title>
    <link rel="stylesheet" href="{{ asset('workbench/theme.css') }}">
</head>
<body class="bg-gray-50 p-8 text-gray-950 dark:bg-gray-950 dark:text-white">
    <main class="mx-auto max-w-4xl space-y-6">
        <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
        <x-matinee::embed
            :data="$page->video"
            class="rounded-xl"
            allow="fullscreen; picture-in-picture"
            allowfullscreen="true"
        />
        <a class="text-primary-600 underline" href="/admin">Edit this page in Filament</a>
    </main>
</body>
</html>
