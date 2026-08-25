<?php
/**
 * The named forms of a page — what <glossary> renders.
 *
 * A description list, because that is what it is: a term, and what the term
 * means. The <div> around each pair is what HTML allows a <dl> to group with,
 * and it is what carries the grid — the three parts of an entry have to be in
 * one box to be laid on three columns.
 *
 * @var string $entries
 */
?>
<dl class="c-glossary">{!! $entries !!}</dl>
