<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Xml\XmlParser;
use App\Content\Xml\XmlSource;

/**
 * Edits the <visuel> of an entity file.
 *
 * Text in, text out, rather than a DOM round-trip: serialising the document
 * again would reflow every file it touches, and a hundred reformatted fiches
 * would bury the one line that matters in the diff.
 *
 * Every edit is read back with the parser afterwards. A file that no longer
 * loads is put back as it was: a tool must not be able to break the corpus.
 *
 * Never writes alt on its own. A description is content, and a machine that
 * invents one writes a plausible sentence about an image it cannot see.
 */
final readonly class VisualWriter
{
    public function __construct(
        private XmlParser $parser,
    ) {}

    /**
     * Writes or replaces the <visuel>.
     *
     * A null value keeps what the file already says, so setting a credit does
     * not drop an alt written ten minutes earlier.
     */
    public function write(
        XmlSource $entity,
        string $file,
        ?string $source = null,
        ?string $alt = null,
        ?string $credit = null,
    ): void {
        $current = Visual::of($this->reread($entity));

        $attributes = array_filter([
            'src' => $file,
            'alt' => $alt ?? $current?->alt,
            'credit' => $credit ?? $current?->credit,
            'source' => $source ?? $current?->source,
        ]);

        $this->edit($entity, function (string $xml) use ($attributes): string {
            // An image arriving means the work is no longer one we decided to
            // leave without.
            $xml = $this->setDeclined($this->withoutElement($xml), false);
            $anchor = $this->anchor($xml);

            if ($anchor === null) {
                throw new MediaException('Ni <resume> ni <titre> où accrocher le visuel.');
            }

            [$offset, $indent] = $anchor;

            return substr($xml, 0, $offset)
                . $this->element($attributes, $indent)
                . substr($xml, $offset);
        });
    }

    /** Takes the image off the fiche. The file itself is Visuals' business. */
    public function remove(XmlSource $entity): void
    {
        $this->edit($entity, fn (string $xml): string => $this->withoutElement($xml));
    }

    /**
     * Says the work will have no image, and that this is fine.
     *
     * The element says which image; the attribute says there will not be one.
     * Without it, a work that nobody has ever photographed sits in the queue
     * for good and the count never means anything.
     */
    public function decline(XmlSource $entity): void
    {
        $this->edit($entity, fn (string $xml): string => $this->setDeclined($this->withoutElement($xml), true));
    }

    /** @param array<string, string> $attributes */
    private function element(array $attributes, string $indent): string
    {
        $written = [];

        foreach ($attributes as $name => $value) {
            $written[] = sprintf('%s="%s"', $name, $this->escape($value));
        }

        // One attribute per line, aligned under the first: these values are long
        // URLs and whole sentences, and a single line would be unreadable.
        return "\n" . $indent . '<visuel '
            . implode("\n" . $indent . str_repeat(' ', strlen('<visuel ')), $written)
            . "/>\n";
    }

    private function withoutElement(string $xml): string
    {
        return preg_replace('~\R?^[ \t]*<visuel\b.*?/>[ \t]*\R~ms', '', $xml, 1) ?? $xml;
    }

    /** Adds or drops visuel="aucun" on the root tag. */
    private function setDeclined(string $xml, bool $declined): string
    {
        return preg_replace_callback(
            '~<(\w+)((?:[^>"]|"[^"]*")*)>~',
            static function (array $match) use ($declined): string {
                $attributes = preg_replace('~\s+visuel="[^"]*"~', '', $match[2]) ?? $match[2];

                return sprintf('<%s%s%s>', $match[1], $attributes, $declined ? ' visuel="aucun"' : '');
            },
            $xml,
            limit: 1,
        ) ?? $xml;
    }

    /**
     * After the summary, or after the title when there is none.
     *
     * @return array{0: int, 1: string}|null offset to insert at, and the indent to match
     */
    private function anchor(string $xml): ?array
    {
        foreach (['</resume>', '</titre>'] as $tag) {
            $position = strpos($xml, $tag);

            if ($position === false) {
                continue;
            }

            $lineStart = strrpos(substr($xml, 0, $position), "\n");
            $lineStart = $lineStart === false ? 0 : $lineStart + 1;
            $lineEnd = strpos($xml, "\n", $position);

            return [
                $lineEnd === false ? strlen($xml) : $lineEnd + 1,
                substr($xml, $lineStart, strspn($xml, " \t", $lineStart)),
            ];
        }

        return null;
    }

    /** The file as it is now, not as it was when the corpus was loaded. */
    private function reread(XmlSource $entity): XmlSource
    {
        return $this->parser->parse($entity->path, $entity->slug);
    }

    /** @param \Closure(string): string $change */
    private function edit(XmlSource $entity, \Closure $change): void
    {
        $original = (string) file_get_contents($entity->path);

        file_put_contents($entity->path, $change($original));

        try {
            $this->reread($entity);
        } catch (\Throwable $exception) {
            file_put_contents($entity->path, $original);

            throw new MediaException($exception->getMessage(), previous: $exception);
        }
    }

    /** Apostrophes are left alone, like everywhere else: attributes are double-quoted here. */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_COMPAT | ENT_XML1, 'UTF-8');
    }
}
