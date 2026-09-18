<?php
/**
 * The aside in the flow — what <note type="…" title="…"> renders.
 *
 * @var string $type
 * @var ?string $title
 * @var string $body
 */
?>
<aside :class="'c-note c-note--' . $type">
    <p :if="$title !== null" class="c-note__title">{{ $title }}</p>
    {!! $body !!}
</aside>
