<?php
/**
 * Gabarit de base. Volontairement sans CSS : HTML sémantique nu.
 */
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Narration Interactive' }}</title>
    <meta :if="$description ?? null" name="description" :content="$description">
</head>
<body>
    <x-slot />
</body>
</html>
