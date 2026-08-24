<?php
/** @var \App\View\NotFoundView $this */
?>
<x-base>
    {{-- Same shape as a content page: an opening on the sheet, the reading
         stepped in by one column. A 404 is a page of this site like any other,
         so it is built like one. --}}
    <div class="c-intro">
        <h1 class="c-intro__title">Rien à cette adresse</h1>

        <p class="c-intro__lead">
            La page a changé de nom, ou elle n'a jamais existé.
        </p>
    </div>

    <article class="c-prose o-canvas__lecture">
        <p>
            Les sections du site sont en haut de page. Si le lien qui vous a
            amené ici venait d'ici, l'erreur est de mon côté :
            <a href="mailto:julien@thylo.be">julien@thylo.be</a>.
        </p>
    </article>
</x-base>
