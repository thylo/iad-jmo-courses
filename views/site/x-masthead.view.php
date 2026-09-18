<?php
/**
 * The band at the top: the site name, and the sections of the site.
 *
 * $sections, $current and $trail come from the view data, supplied by
 * App\View\NavigationViewProcessor to any view implementing HasNavigation.
 * They are not defended against here: a page that renders the frame without
 * the contract should break, not render a band with no sections in it.
 *
 * Only the top level. A section's own pages are listed by its index page —
 * /cours names its courses, /oeuvres indexes its œuvres — so unfolding the tree
 * here would repeat, on every page, what the section already says better on one.
 *
 * @var \App\Content\NavNode[] $sections
 * @var string $current
 * @var \App\Content\Trail $trail
 */
?>
<header class="c-masthead c-rule-under">
    <a class="c-masthead__home" href="/">thylo<span class="c-masthead__tld">.be</span></a>

    <nav class="c-nav" aria-label="Sections du site">
        <ul class="c-nav__list">
            <li :foreach="$sections as $section">
                <span :if="$section->isLabel()" class="c-nav__label">{{ $section->title }}</span>

                {{-- The section you are reading inside is marked, but only the
                     page you are actually on claims to be the current page.

                     Inside is the trail's business, not the folders': a concept
                     fiche filed in content/cours/ but reached through
                     /panorama/structures marks Panorama, which is what the line
                     above its title says too. --}}
                <a
                    :else
                    :href="$section->slug"
                    :class="$trail->contains($section->slug) ? 'c-nav__link c-nav__link--within' : 'c-nav__link'"
                    :aria-current="$section->slug === $current ? 'page' : false"
                >{{ $section->title }}</a>
            </li>
        </ul>
    </nav>
</header>
