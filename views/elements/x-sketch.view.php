<?php
/**
 * A p5 sketch — what <sketch> renders.
 *
 * The switch and the empty render panel are in the markup but hidden: they
 * only mean something once <p5-sketch> is defined, which un-hides the switch
 * and wires the roles. Until then, or without JavaScript, the figure is the
 * code block and its caption.
 *
 * The two labels live here rather than in the script, with the rest of the
 * site's French.
 *
 * @var string  $code     the highlighted block, already rendered
 * @var string  $scripts  URLs to load before the sketch, space-separated
 * @var string  $size     custom properties for the canvas, or ''
 * @var ?string $caption  already rendered: it goes through the prose pipeline
 */
?>
<figure class="c-sketch" style="{{ $size }}">
    <p5-sketch class="c-sketch__frame" data-scripts="{{ $scripts }}">
        <div class="c-sketch__switch" data-switch hidden>
            <button type="button" class="c-sketch__tab" data-view="code">Code</button>
            <button type="button" class="c-sketch__tab" data-view="render">Rendu</button>
        </div>
        <div class="c-sketch__code" data-panel="code">{!! $code !!}</div>
        <div class="c-sketch__render" data-panel="render" hidden></div>
    </p5-sketch>
    <figcaption :if="$caption !== null" class="c-sketch__caption">{!! $caption !!}</figcaption>
</figure>
