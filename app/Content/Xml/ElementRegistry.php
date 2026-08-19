<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Xml\Elements\BacklinksElement;
use App\Content\Xml\Elements\ImageElement;
use App\Content\Xml\Elements\IndexElement;
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
        IndexElement $index,
        BacklinksElement $backlinks,
    ) {
        $this->renderers = [
            'markdown' => $markdown,
            'section' => $section,
            'note' => $note,
            'marge' => $sidenote,
            'video' => $video,
            'image' => $image,
            'liste' => $list,
            'item' => $item,
            'index' => $index,
            'retroliens' => $backlinks,
        ];
    }

    public function knows(string $name): bool
    {
        return isset($this->renderers[$name]);
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
