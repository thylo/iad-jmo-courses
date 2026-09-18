<?php
/**
 * The frame: a band carrying the site name and its sections, the reading, a
 * closing note. Everything is a direct child of the page grid — see
 * app/css/4-objects/canvas.css for what the named columns are.
 *
 * This name replaces the framework's own <x-base>: a project component always
 * wins over a vendor one, so the file being here is the whole of the override.
 *
 * $title and $description come from the view data, so a page sets them on its
 * view object rather than passing them back down as props.
 *
 * Styles come from the ITCSS entrypoint (app/main.entrypoint.css), bundled by
 * Vite and injected through <x-vite-tags />.
 *
 * @var ?string $title
 * @var ?string $description
 */
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'thylo.be' }}</title>
    <meta :if="$description ?? null" name="description" :content="$description">
    <x-vite-tags />
</head>
<body>
<div class="o-canvas">
    <x-masthead />

    <x-slot />

    <x-colophon />
</div>
</body>
</html>
