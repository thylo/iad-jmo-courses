<?php
/**
 * Base template. Styles come from the ITCSS entrypoint (app/main.entrypoint.css),
 * bundled by Vite and injected through <x-vite-tags />.
 *
 * The frame: a band carrying the site name and its sections, the reading, a
 * closing note. Everything is a direct child of the page grid — see
 * app/css/4-objects/canvas.css for what the named columns are.
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
    <header class="c-masthead c-trait-dessous">
        <a class="c-masthead__home" href="/">Thylo</a>

        <nav class="c-nav" aria-label="Sections du site">
            {!! $sections ?? '' !!}
        </nav>
    </header>

    <x-slot />

    <footer class="c-colophon c-trait-dessus">
        <p class="c-colophon__text">
            Ce site n'est pas figé : c'est un espace de travail qui se refait au fil
            des semestres, des retours des étudiants et des essais ratés.
            <a href="mailto:julien@thylo.be">julien@thylo.be</a>
        </p>
    </footer>
</div>
</body>
</html>
