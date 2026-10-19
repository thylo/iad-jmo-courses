<?php
/**
 * The closing note, the same on every page. It names how the content was
 * written and points to the full account on the about page, so a reader
 * landing on an article from a search engine knows it too.
 *
 * It sits on the page grid like the reading does: the account in the text
 * column, its rating hung in the margin as the label of its paragraph, and the
 * practical pointers in the field. See app/css/5-components/colophon.css.
 *
 * The frame is literal text rather than content, so App\Content\FrenchSpacing
 * never sees it: the insécables before the colons are written here by hand.
 */
?>
<footer class="c-colophon c-rule-over">
    <p class="c-colophon__text c-colophon__text--first">
        Ce site n'est pas figé&nbsp;: c'est un espace de travail qui se refait au fil
        des semestres, des retours des étudiants et des essais ratés. Il est écrit
        avec Claude, à partir de mes notes&nbsp;:
        <a href="/a-propos/colophon">pourquoi, et avec quelles limites</a>.
    </p>

    <p class="c-colophon__label">
        <a href="https://daliascale.org/fr/">D2</a>
    </p>

    <p class="c-colophon__text c-colophon__text--rated">
        Sur l'échelle DALIA, c'est le niveau D2&nbsp;: l'IA écrit des morceaux et
        reformule, sans changer ce que le texte dit. L'idée de départ et la décision
        finale restent les miennes.
    </p>

    <div class="c-colophon__aside">
        <p>
            Il tourne sur un moteur de site maison basé sur
            <a href="https://tempestphp.com">Tempest</a>.
        </p>
        <p>Toutes ses sections sont réunies sur le <a href="/plan-du-site">plan du site</a>.</p>
        <p><a href="mailto:julien@thylo.be">julien@thylo.be</a></p>
    </div>
</footer>
