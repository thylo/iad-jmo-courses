<?php
/**
 * The index of where a page sends you — what <destinations> renders.
 *
 * @var string $entries
 * @var bool $single
 */
?>
<ul :class="$single ? 'c-destinations c-destinations--single' : 'c-destinations'">{!! $entries !!}</ul>
