<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Xml\Elements\BacklinksElement;
use App\Content\Xml\Elements\DestinationElement;
use App\Content\Xml\Elements\DestinationsElement;
use App\Content\Xml\Elements\GridElement;
use App\Content\Xml\Elements\ImageElement;
use App\Content\Xml\Elements\ItemElement;
use App\Content\Xml\Elements\ListElement;
use App\Content\Xml\Elements\MarkdownElement;
use App\Content\Xml\Elements\NoteElement;
use App\Content\Xml\Elements\SectionElement;
use App\Content\Xml\Elements\SidenoteElement;
use App\Content\Xml\Elements\VideoElement;

/**
 * The rendering elements, shared by every entity type.
 *
 * Adding a block to the vocabulary is adding a class and one line here. The
 * parser reads this too, so an unknown tag is caught at load time rather than
 * silently dropped from the page.
 */
final readonly class ElementRegistry
{
    /**
     * Blocks whose content IS text rather than a tree of blocks.
     *
     * Everywhere else, loose prose between tags is a mistake with a fix — the
     * parser says so and points at <markdown>. These two are the exceptions,
     * and for two different reasons: <markdown> is opaque, its content is a
     * document in another language; <destination> holds one line describing
     * where it sends you, which is a value, no more a tree than a <titre> is.
     *
     * Their element children are still checked. Text is allowed in, an unknown
     * tag is not.
     */
    private const array HOLDS_TEXT = ['markdown', 'destination'];

    /** @var array<string, ElementRenderer> */
    private array $renderers;

    public function __construct(
        MarkdownElement $markdown,
        SectionElement $section,
        NoteElement $note,
        SidenoteElement $sidenote,
        VideoElement $video,
        ImageElement $image,
        ListElement $list,
        ItemElement $item,
        GridElement $grid,
        BacklinksElement $backlinks,
        DestinationsElement $destinations,
        DestinationElement $destination,
    ) {
        $this->renderers = [
            'markdown' => $markdown,
            'section' => $section,
            'note' => $note,
            'sidenote' => $sidenote,
            'video' => $video,
            'image' => $image,
            'list' => $list,
            'item' => $item,
            'grid' => $grid,
            'backlinks' => $backlinks,
            'destinations' => $destinations,
            'destination' => $destination,
        ];
    }

    public function knows(string $name): bool
    {
        return isset($this->renderers[$name]);
    }

    public function holdsText(string $name): bool
    {
        return in_array($name, self::HOLDS_TEXT, true);
    }

    public function get(string $name): ?ElementRenderer
    {
        return $this->renderers[$name] ?? null;
    }

    /** @return string[] */
    public function names(): array
    {
        return array_keys($this->renderers);
    }
}
