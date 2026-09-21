<?php
/**
 * The closing note, the same on every page. It names how the content was
 * written and points to the full account on the about page, so a reader
 * landing on an article from a search engine knows it too.
 *
 * The frame is literal text rather than content, so App\Content\FrenchSpacing
 * never sees it: the insécables before the colons are written here by hand.
 */
?>
<footer class="c-colophon c-rule-over">
    <p class="c-colophon__text">
        Ce site n'est pas figé&nbsp;: c'est un espace de travail qui se refait au fil
        des semestres, des retours des étudiants et des essais ratés. La majorité
        du contenu a été générée avec Claude à partir de mes notes&nbsp;:
        <a href="/a-propos/colophon">pourquoi, et avec quelles limites</a>.
        Il tourne sur un moteur de site maison basé sur
        <a href="https://tempestphp.com">Tempest</a>.
        Toutes ses sections sont réunies sur le <a href="/plan-du-site">plan du site</a>.
        <a href="mailto:julien@thylo.be">julien@thylo.be</a>
    </p>
</footer>
