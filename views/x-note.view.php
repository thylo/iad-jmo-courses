<?php
/**
 * The aside in the flow — what <note type="…" title="…"> renders.
 *
 * @var string $type
 * @var ?string $title
 * @var string $body
 */
?>
<aside :class="'note note--' . $type">
    <p :if="$title !== null" class="note__title">{{ $title }}</p>
    {!! $body !!}
</aside>
