<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\ContentRepository;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;

/**
 * Reasons to look twice at an image a machine brought back.
 *
 * Three signals, all cheap, all found on the real corpus rather than imagined:
 * two works sharing a file byte for byte turned out to be the two parked
 * domains; a page that answered from another domain than the one the fiche
 * names is either a work that moved or a registrar's ad; an image narrower than
 * the text column cannot be a screenshot of anything.
 *
 * None of them proves an image is wrong. They put the eight worth opening first,
 * so the sorting pass takes a coffee rather than an evening.
 */
final readonly class VisualDoubts
{
    /** The narrowest variant the layout serves at 1×. Below it, nothing is gained. */
    private const int SMALL = 640;

    public function __construct(
        private ContentRepository $content,
        private SchemaRegistry $schemas,
        private Visuals $visuals,
        private Images $images,
    ) {}

    /** @return array<string, string[]> entity id => what to check */
    public function all(): array
    {
        $files = [];
        $doubts = [];

        foreach ($this->content->sources() as $entity) {
            if ($this->schemas->get($entity->type)?->field('visuel') === null) {
                continue;
            }

            $visual = Visual::of($entity);

            if ($visual === null) {
                continue;
            }

            $path = $this->visuals->path($entity, $visual);

            if (! is_file($path)) {
                continue;
            }

            $files[(string) md5_file($path)][] = $entity->id;

            $reasons = array_filter([
                $this->tooSmall($path),
                $this->movedHost($entity, $visual),
            ]);

            if ($reasons !== []) {
                $doubts[$entity->id] = array_values($reasons);
            }
        }

        return $this->withDuplicates($doubts, $files);
    }

    private function tooSmall(string $path): ?string
    {
        [$width] = $this->images->dimensions($path);

        return $width < self::SMALL ? sprintf('petite : %dpx de large', $width) : null;
    }

    /** The fiche names one address, the image came from another. */
    private function movedHost(XmlSource $entity, Visual $visual): ?string
    {
        $announced = $this->domain($entity->value('url') ?? '');
        $reached = $this->domain($visual->source ?? '');

        if ($announced === '' || $reached === '' || $announced === $reached) {
            return null;
        }

        return sprintf('la page a mené à %s', $reached);
    }

    /**
     * @param array<string, string[]> $doubts
     * @param array<string, string[]> $files
     * @return array<string, string[]>
     */
    private function withDuplicates(array $doubts, array $files): array
    {
        foreach ($files as $shared) {
            if (count($shared) < 2) {
                continue;
            }

            foreach ($shared as $id) {
                $others = array_values(array_diff($shared, [$id]));
                $doubts[$id][] = sprintf('fichier identique à %s', implode(', ', $others));
            }
        }

        return $doubts;
    }

    /** Enough of a domain to compare two of them: "www.m.youtube.com" and "youtube.com" are one site. */
    private function domain(string $url): string
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
        $labels = explode('.', $host);

        return implode('.', array_slice($labels, -2));
    }
}
