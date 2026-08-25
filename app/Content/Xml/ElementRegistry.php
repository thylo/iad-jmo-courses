<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Xml\Elements\BacklinksElement;
use App\Content\Xml\Elements\DestinationElement;
use App\Content\Xml\Elements\DestinationsElement;
use App\Content\Xml\Elements\DiagramElement;
use App\Content\Xml\Elements\GlossaryElement;
use App\Content\Xml\Elements\GridElement;
use App\Content\Xml\Elements\ImageElement;
use App\Content\Xml\Elements\ItemElement;
use App\Content\Xml\Elements\ListElement;
use App\Content\Xml\Elements\MarkdownElement;
use App\Content\Xml\Elements\NoteElement;
use App\Content\Xml\Elements\PreambleElement;
use App\Content\Xml\Elements\QuoteElement;
use App\Content\Xml\Elements\SectionElement;
use App\Content\Xml\Elements\SidenoteElement;
use App\Content\Xml\Elements\SpotifyElement;
use App\Content\Xml\Elements\TermElement;
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
     * parser says so and points at <markdown>. These four are the exceptions,
     * and for two different reasons: <markdown> and <preamble> are opaque,
     * their content is a document in another language; <destination> and <term>
     * each hold one line — where it sends you, what the form is — which is a
     * value, no more a tree than a <titre> is.
     *
     * Their element children are still checked. Text is allowed in, an unknown
     * tag is not.
     */
    private const array HOLDS_TEXT = ['markdown', 'preamble', 'destination', 'term'];

    /** @var array<string, ElementRenderer> */
    private array $renderers;

    public function __construct(
        MarkdownElement $markdown,
        PreambleElement $preamble,
        SectionElement $section,
        NoteElement $note,
        QuoteElement $quote,
        SidenoteElement $sidenote,
        VideoElement $video,
        SpotifyElement $spotify,
        ImageElement $image,
        DiagramElement $diagram,
        ListElement $list,
        ItemElement $item,
        GridElement $grid,
        GlossaryElement $glossary,
        TermElement $term,
        BacklinksElement $backlinks,
        DestinationsElement $destinations,
        DestinationElement $destination,
    ) {
        $this->renderers = [
            'markdown' => $markdown,
            'preamble' => $preamble,
            'section' => $section,
            'note' => $note,
            'quote' => $quote,
            'sidenote' => $sidenote,
            'video' => $video,
            'spotify' => $spotify,
            'image' => $image,
            'diagram' => $diagram,
            'list' => $list,
            'item' => $item,
            'grid' => $grid,
            'glossary' => $glossary,
            'term' => $term,
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
