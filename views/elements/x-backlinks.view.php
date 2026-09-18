<?php
/**
 * Who points here — what <backlinks/> renders.
 *
 * Every entry came out of the index, so every entry resolves: there is no
 * c-missing-link case to carry here, unlike a link written in the prose.
 *
 * One group per kind of source. Only the title is a link; the year and the
 * credit around it are the label on the wall, not the door.
 *
 * A group is named only when there is another to tell it from. Alone, its
 * name repeats what the list already shows.
 *
 * @var string $title
 * @var string $id
 * @var array<int, array{label: string, dated: bool, entries: array<int, array{href: string, label: string, credit: ?string, year: ?string, repeat: bool}>}> $groups
 */
?>
<nav class="c-backlinks" :aria-labelledby="$id">
    <h2 class="c-backlinks__title" :id="$id">{{ $title }}</h2>

    <section :foreach="$groups as $group" class="c-backlinks__group">
        <h3 :if="count($groups) > 1" class="c-backlinks__kind">{{ $group['label'] }}</h3>

        <ul :class="$group['dated'] ? 'c-backlinks__list c-backlinks__list--dated' : 'c-backlinks__list'">
            <li :foreach="$group['entries'] as $entry" class="c-backlinks__entry">
                <span :if="$entry['year'] !== null" :class="$entry['repeat'] ? 'c-backlinks__year u-visually-hidden' : 'c-backlinks__year'">{{ $entry['year'] }}</span>
                <span class="c-backlinks__name"><a :href="$entry['href']">{{ $entry['label'] }}</a><span :if="$entry['credit'] !== null" class="c-backlinks__credit">{{ $entry['credit'] }}</span></span>
            </li>
        </ul>
    </section>
</nav>
