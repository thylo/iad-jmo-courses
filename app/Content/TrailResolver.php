<?php

declare(strict_types=1);

namespace App\Content;

use Tempest\Container\Singleton;

/**
 * Builds the trail of a page by walking up, one parent at a time.
 *
 * The folders under content/ are not where the reader is. A concept fiche is
 * filed in content/cours/concepts/ because that is where the fiches live, but
 * nobody arrives at it from there: it is reached from /panorama/structures,
 * which names it. So the trail follows the LINKS between pages, and only falls
 * back on the folders when nothing names the page — which is the honest answer
 * for an œuvre, reached from the index of œuvres and from nowhere else.
 *
 * Three sources of a parent, in this order:
 *
 *   1. <parent ref="structures"/>, written on the page itself. Both an answer
 *      when the graph is silent and a correction when it is wrong.
 *   2. The page that names this one — <destination to="…">, <term to="…">.
 *      First one wins, in the reading order of content/.
 *   3. The folder above, if it has an index page.
 *
 * The homepage is never a crumb: the masthead's wordmark is a link home on
 * every page, so opening the trail with it would be the site telling the reader
 * twice about the one place they already know how to reach.
 */
#[Singleton]
final class TrailResolver
{
    private const string HOME = '/';

    /**
     * The two elements that send the reader from one page to a named other.
     *
     * A <grid> does not count, and that is the whole distinction: a grid states
     * a query — "every concept whose genre is structure" — and the answer
     * changes with the corpus. Being listed by a query is not being placed
     * somewhere; being named is.
     */
    private const array NAMING_ELEMENTS = ['destination', 'term'];

    /** @var array<string, string>|null child slug => parent slug */
    private ?array $parents = null;

    public function __construct(
        private readonly ContentRepository $content,
    ) {}

    public function to(string $slug): Trail
    {
        $documents = $this->content->all();
        $crumbs = [];

        // A page reachable from itself would loop forever. Content can say
        // that — two pages naming each other — so the walk refuses to revisit
        // rather than trusting the graph to be a tree.
        $seen = [$slug => true];

        while (($parent = $this->parentOf($slug)) !== null) {
            if ($parent === self::HOME || isset($seen[$parent]) || ! isset($documents[$parent])) {
                break;
            }

            $seen[$parent] = true;
            $crumbs[] = new Crumb(slug: $parent, title: $documents[$parent]->title);
            $slug = $parent;
        }

        return new Trail(array_reverse($crumbs));
    }

    private function parentOf(string $slug): ?string
    {
        return $this->parents()[$slug] ?? $this->folderParent($slug);
    }

    /**
     * The links, read the way the trail needs them: child first, parent second.
     *
     * This is the reverse of what the files say, like ContentIndex's backlinks
     * — and for the same reason. A page names what it sends you to; the page
     * you land on has to be able to ask what sent you.
     *
     * @return array<string, string> child slug => parent slug
     */
    private function parents(): array
    {
        if ($this->parents !== null) {
            return $this->parents;
        }

        /** @var array<string, string> id => slug */
        $slugs = [];

        foreach ($this->content->sources() as $source) {
            $slugs[$source->id] = $source->slug;
        }

        $named = [];
        $declared = [];

        foreach ($this->content->sources() as $source) {
            $parent = $source->value('parent');

            // An unresolved ref is not a place. content:check reports it with
            // every other dead reference, which is where it belongs.
            if ($parent !== null && isset($slugs[$parent])) {
                $declared[$source->slug] = $slugs[$parent];
            }

            foreach (self::NAMING_ELEMENTS as $tag) {
                foreach ($source->root->getElementsByTagName($tag) as $element) {
                    $child = $slugs[Html::attribute($element, 'to')] ?? null;

                    if ($child !== null) {
                        $named[$child] ??= $source->slug;
                    }
                }
            }
        }

        // What a page says about itself beats what another page says about it.
        return $this->parents = [...$named, ...$declared];
    }

    /**
     * The nearest folder above that has an index page.
     *
     * Nearest rather than immediate: a folder without an index is a label in
     * the navigation, and a label is not somewhere a reader can be sent.
     */
    private function folderParent(string $slug): ?string
    {
        $documents = $this->content->all();

        while (true) {
            $cut = strrpos($slug, '/');

            // Position 0 is the homepage, and the trail does not begin there.
            if ($cut === false || $cut === 0) {
                return null;
            }

            $slug = substr($slug, 0, $cut);

            if (isset($documents[$slug])) {
                return $slug;
            }
        }
    }
}
