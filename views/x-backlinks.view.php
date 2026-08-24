<?php
/**
 * Who points here — what <backlinks/> renders.
 *
 * Every entry came out of the index, so every entry resolves: there is no
 * missing-link case to carry here, unlike a link written in the prose.
 *
 * @var string $title
 * @var string $id
 * @var array<int, array{href: string, label: string}> $entries
 */
?>
<nav class="c-backlinks">
    <h2 class="c-backlinks__title" :id="$id">{{ $title }}</h2>

    <ul class="c-backlinks__list">
        <li :foreach="$entries as $entry"><a :href="$entry['href']">{{ $entry['label'] }}</a></li>
    </ul>
</nav>
