<?php
/**
 * The index of where a page sends you — what <destinations> renders.
 *
 * The classes arrive composed, the way <x-figure> takes its own: whether the
 * index drops its columns and whether it is the way into the page are two
 * separate answers, and DestinationsElement is where both are decided.
 *
 * @var string $class
 * @var string $entries
 */
?>
<ul :class="$class">{!! $entries !!}</ul>
