<?php
/**
 * Base template. Styles come from the ITCSS entrypoint (app/main.entrypoint.css),
 * bundled by Vite and injected through <x-vite-tags />.
 *
 * The frame: a masthead carrying the site name and its sections, the reading,
 * a closing note. The left column of the grid is the margin — it holds the
 * labels and the margin notes, and nothing that navigates.
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
    <div class="o-page o-stack o-stack--section">
        <header class="o-grid c-masthead">
            <p class="o-grid__aside c-masthead__origin">Thylo</p>

            <div class="o-grid__main">
                <nav class="c-nav" aria-label="Sections du site">
                    {!! $sections ?? '' !!}
                </nav>
            </div>
        </header>

        <x-slot />

        <footer class="o-grid c-colophon">
            <p class="o-grid__main c-colophon__text">
                Ce site n'est pas figé : c'est un espace de travail qui se refait au fil
                des semestres, des retours des étudiants et des essais ratés.
                <a href="mailto:julien@thylo.be">julien@thylo.be</a>
            </p>
        </footer>
    </div>
</body>
</html>
