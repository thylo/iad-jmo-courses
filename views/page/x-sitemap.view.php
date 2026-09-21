<?php
/**
 * The whole site, as one nested list.
 *
 * Set as prose, because to the reader it is a list like any other on the site:
 * .c-prose already knows how to indent one and hang its markers.
 *
 * Three levels, written out: a section, its pages, the pages of a folder inside
 * it. That is as deep as content/ goes. Tempest expands components at compile
 * time, so the template cannot recurse; a fourth level of folders would need a
 * fourth loop here.
 *
 * Entities are not in it, for the reason the tree gives: the œuvres are one
 * index, /oeuvres, and that index is on the list. sitemap.xml, which is read
 * by machines rather than people, lists every one of them.
 *
 * $sections comes from the view data, supplied by
 * App\View\NavigationViewProcessor.
 *
 * @var \App\Content\NavNode[] $sections
 */
?>
<nav class="c-prose" aria-label="Plan du site">
    <ul>
        <li :foreach="$sections as $section">
            <span :if="$section->isLabel()">{{ $section->title }}</span>
            <a :else :href="$section->slug">{{ $section->title }}</a>

            <ul :if="$section->children !== []">
                <li :foreach="$section->children as $page">
                    <span :if="$page->isLabel()">{{ $page->title }}</span>
                    <a :else :href="$page->slug">{{ $page->title }}</a>

                    <ul :if="$page->children !== []">
                        <li :foreach="$page->children as $subpage">
                            <span :if="$subpage->isLabel()">{{ $subpage->title }}</span>
                            <a :else :href="$subpage->slug">{{ $subpage->title }}</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</nav>
