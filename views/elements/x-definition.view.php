<?php
/**
 * The gloss under a title — what <definition> renders.
 *
 * App\Content\Intro lifts it by this exact opening tag, so the class is a
 * contract with that file and not only a hook for the stylesheet.
 *
 * @var string $html  already rendered: it goes through the prose pipeline
 */
?>
<p class="c-intro__definition">{!! $html !!}</p>
