<?php
/**
 * The "home" layout: the shared body, plus the three marks that belong to the
 * homepage alone. A page asks for it with layout="home".
 *
 * The three are one gesture, in order down the page: a face the title is
 * written over, a wobble where the introduction hands over to the reading, a
 * signature at the foot. They only work as an introduction to the person
 * writing, which happens once — so they live here rather than in x-document,
 * and no other page has to step around them.
 *
 * All three are drawings, and all three are decoration: the name is already in
 * the masthead and the words are already in content/index.xml, so a screen
 * reader has nothing to gain from them.
 *
 * @var \App\View\DocumentView $this
 */
?>
<x-base>
    <x-document>
        <x-slot name="opening">
            <div class="c-portrait" aria-hidden="true"></div>
        </x-slot>

        <x-slot name="reading">
            <div class="c-wobble" aria-hidden="true"></div>
        </x-slot>

        <x-slot name="closing">
            <div class="c-signature" aria-hidden="true"></div>
        </x-slot>
    </x-document>
</x-base>
