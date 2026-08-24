<?php
/**
 * The "home" layout: the shared body, plus the two marks that belong to the
 * homepage alone. A page asks for it with layout="home".
 *
 * The drawn rule under the title and the portrait in the reading only work as
 * an introduction to the person writing, which happens once. They live here
 * rather than in x-document, so no other page has to step around them.
 *
 * @var \App\View\DocumentView $this
 */
?>
<x-base>
    <x-document>
        <x-slot name="opening">
            <div class="c-signature" aria-hidden="true">coucou</div>
        </x-slot>

        <x-slot name="reading">
            <div class="c-portrait" aria-hidden="true"></div>
        </x-slot>
    </x-document>
</x-base>
