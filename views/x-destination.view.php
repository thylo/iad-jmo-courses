<?php
/**
 * One door — what <destination to="…"> renders.
 *
 * The whole entry is the target, description included. Unresolved is not fatal
 * and not silent either: the entry stays in the index without an href, says so,
 * and content:check reports it.
 *
 * The arrow shows at rest rather than on hover — it is what says "this leads
 * somewhere" before the pointer has been anywhere. Drawn on the 20px grid of
 * the other icons, in currentColor so it follows the link.
 *
 * @var ?string $href
 * @var string $label
 * @var string $description
 */
?>
<li class="c-destinations__entry">
    <a :class="$href === null ? 'c-destinations__link c-missing-link' : 'c-destinations__link'" :href="$href">
        <span class="c-destinations__name"><span class="c-destinations__label">{{ $label }}</span><svg class="c-destinations__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10h11"></path><path d="M11 6l4 4-4 4"></path></svg></span>
        <span class="c-destinations__description">{{ $description }}</span>
    </a>
</li>
