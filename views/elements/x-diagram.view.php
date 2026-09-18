<?php
/**
 * A drawing, inlined so it inherits the ink of the text around it.
 *
 * The <svg> goes in verbatim: SVGO has already stripped it to a viewBox and
 * currentColor, and nothing in PHP rewrites it. The wrapper is what carries
 * the class and the description, which keeps the escaping here — where every
 * other attribute on the site is escaped — rather than in a regex.
 *
 * Described or hidden, never half of each: a role="img" with no label is a
 * thing a screen reader announces and cannot name.
 *
 * @var string $svg  the file's contents
 * @var string $alt  empty when nobody has described the drawing yet
 */
?>
<div :if="$alt !== ''" class="c-diagram" role="img" aria-label="{{ $alt }}">{!! $svg !!}</div>
<div :else class="c-diagram" aria-hidden="true">{!! $svg !!}</div>
