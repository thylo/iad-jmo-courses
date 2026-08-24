<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\ContentRepository;
use App\Content\Html;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;

/**
 * What is left to do on the images, as four lists.
 *
 * The queue of works still to illustrate is the same idea as the queue of
 * entities still to write: not a set of mistakes, a set of things to do. The
 * alt texts are 141 sentences that only a human can write, so the count has to
 * exist from the first image rather than from the hundredth.
 *
 * Returns data, not lines: content:check prints it today, a page can show it
 * tomorrow.
 */
final readonly class MediaCheck
{
    public function __construct(
        private ContentRepository $content,
        private SchemaRegistry $schemas,
        private MediaLibrary $library,
    ) {}

    public function run(): MediaCheckReport
    {
        $withoutVisual = [];
        $withoutAlt = [];
        $declined = [];
        $missing = [];
        $unbuilt = [];
        $used = [];
        $total = 0;

        foreach ($this->content->sources() as $entity) {
            // Before the type check: a page has no <visuel>, but it can still
            // carry an <image>, and that file is used like any other.
            foreach ($this->blockImages($entity) as $path) {
                $used[$path] = true;
            }

            if ($this->schemas->get($entity->type)?->field('visuel') === null) {
                continue;
            }

            $total++;
            $visual = Visual::of($entity);

            if ($visual === null) {
                // Decided to have none: not a hole, and not counted as one.
                if (Html::attribute($entity->root, 'visuel') === 'aucun') {
                    $declined[] = $entity->id;
                } else {
                    $withoutVisual[] = $entity->id;
                }

                continue;
            }

            $path = MediaLibrary::sourcePath(Visual::directory($entity->type), $visual->file);
            $used[$path] = true;

            if (! is_file($path)) {
                $missing[$entity->id] = $visual->file;
            } elseif (! $this->library->has($visual->asset)) {
                // The renderer stays quiet about this on purpose; the count is
                // what keeps it from going unnoticed.
                $unbuilt[] = $entity->id;
            }

            if ($visual->alt === null) {
                $withoutAlt[] = $entity->id;
            }
        }

        return new MediaCheckReport(
            total: $total,
            withoutVisual: $withoutVisual,
            withoutAlt: $withoutAlt,
            declined: $declined,
            unbuilt: $unbuilt,
            missing: $missing,
            orphans: array_values(array_filter(
                $this->originals(),
                static fn (string $path): bool => ! isset($used[$path]),
            )),
        );
    }

    /**
     * The files an <image> block cites, anywhere in a document.
     *
     * The orphan list used to be built from the <visuel> fields alone, so the
     * only picture on /oeuvres — a page, which has no such field — read as a
     * file nobody uses. An image in the prose is a use like any other.
     *
     * @return string[] absolute paths
     */
    private function blockImages(XmlSource $source): array
    {
        $paths = [];

        foreach ($source->root->getElementsByTagName('image') as $element) {
            $visual = Visual::from($element, $source->type);

            if ($visual !== null) {
                $paths[] = MediaLibrary::sourcePath(Visual::directory($source->type), $visual->file);
            }
        }

        return $paths;
    }

    /** @return string[] absolute paths of everything sitting in media/ */
    private function originals(): array
    {
        $root = MediaLibrary::sourcePath();

        if (! is_dir($root)) {
            return [];
        }

        $paths = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths, SORT_NATURAL);

        return $paths;
    }
}
