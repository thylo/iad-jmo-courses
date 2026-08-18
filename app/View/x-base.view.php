<?php
/**
 * Base template. Styles come from the ITCSS entrypoint (app/main.entrypoint.css),
 * bundled by Vite and injected through <x-vite-tags />.
 */
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Narration Interactive' }}</title>
    <meta :if="$description ?? null" name="description" :content="$description">
    <x-vite-tags />
</head>
<body>
    <x-slot />
</body>
</html>
