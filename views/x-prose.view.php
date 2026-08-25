<?php
/**
 * A block of prose — what <markdown> produces.
 *
 * The wrapper is the whole component: what it holds is bare HTML that markdown
 * wrote, and .c-prose is the licence to style bare elements. See prose.css.
 *
 * The class is given rather than fixed because one caller needs a second one on
 * the same box: <preamble> is prose that App\Content\Intro lifts into the
 * opening, and it is lifted by matching this <div>. A wrapper around the
 * wrapper would nest two divs and put the closing tag the match needs in the
 * wrong place — so the mark goes on the box that is already there.
 *
 * @var string $class
 * @var string $html
 */
?>
<div class="{{ $class }}">{!! $html !!}</div>
