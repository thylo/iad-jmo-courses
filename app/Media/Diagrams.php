<?php

declare(strict_types=1);

namespace App\Media;

use Tempest\Container\Singleton;

/**
 * The drawings in media/diagrams/, read from disk and inlined in the page.
 *
 * They sit in media/ like every other original — content, versioned next to
 * the text they illustrate — but nothing in public/media/ answers for them, and
 * media:build ignores them: its EXTENSIONS list is raster only. A drawing has
 * no variants to build. It is one file, and it is already the right size at
 * every size.
 *
 * Inlined rather than served as <img src="….svg">, and that is the whole point:
 * an SVG in an <img> is a separate document. It cannot see the page's colours,
 * so currentColor resolves to black and the drawing disappears on dark paper.
 * Inlined, it inherits the ink of the paragraph above it.
 *
 * Which is also why nothing here rewrites the file. What makes a drawing usable
 * — a viewBox and no pixel size, currentColor instead of a hard-coded black —
 * is done once by SVGO (see svgo.config.js), not on every request.
 */
#[Singleton]
final class Diagrams
{
    /** Under media/, and nowhere near public/media/: there is nothing to build. */
    public const string DIRECTORY = 'diagrams';

    /** @var array<string, string> file name => contents, read at most once per request */
    private array $read = [];

    public static function path(string $file): string
    {
        return MediaLibrary::sourcePath(self::DIRECTORY, $file);
    }

    /**
     * A bare .svg file name, and nothing that could climb out of the folder.
     *
     * The content files are ours, so this is not a defence against an attacker.
     * It is a defence against a src= that quietly reads something else and
     * pastes it into a public page — the one mistake this element could make
     * that the writer would never see.
     */
    public static function isName(string $file): bool
    {
        return $file !== ''
            && ! str_contains($file, '/')
            && ! str_contains($file, '\\')
            && ! str_contains($file, '..')
            && str_ends_with($file, '.svg');
    }

    /**
     * The file's contents, or an empty string.
     *
     * Silent on absence, like ImageTag and for the same reason: a page whose
     * drawing was never made still says everything it said before. The sanction
     * stays smaller than the fault, and content:check counts what is missing.
     */
    public function svg(string $file): string
    {
        if (! self::isName($file)) {
            return '';
        }

        return $this->read[$file] ??= $this->load(self::path($file));
    }

    private function load(string $path): string
    {
        if (! is_file($path)) {
            return '';
        }

        $contents = trim((string) file_get_contents($path));

        // What goes out is inlined verbatim into the page, so what comes in has
        // to be a drawing and not whatever else was left in the folder.
        return str_starts_with($contents, '<svg') ? $contents : '';
    }
}
