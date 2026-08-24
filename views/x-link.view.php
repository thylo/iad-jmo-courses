<?php
/**
 * An entity, as a link. The one place an id becomes an anchor.
 *
 * Unresolved is not fatal: you write before you create the target. The anchor
 * stays in the flow, without an href, and content:check reports it.
 *
 * Inline, so it holds no whitespace of its own.
 *
 * @var ?string $href
 * @var string $label
 */
?>
<a :if="$href !== null" :href="$href">{{ $label }}</a><a :else class="missing-link">{{ $label }}</a>
