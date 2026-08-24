<?php
/**
 * A block of prose — what <markdown> produces.
 *
 * The wrapper is the whole component: what it holds is bare HTML that markdown
 * wrote, and .c-prose is the licence to style bare elements. See prose.css.
 *
 * @var string $html
 */
?>
<div class="c-prose">{!! $html !!}</div>
