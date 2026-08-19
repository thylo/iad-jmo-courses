<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\ContentRepository;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;

/**
 * What is left for a human to look at, in the order that makes sense.
 *
 * Images first, holes second. Sorting what a machine brought back is half an
 * hour of work that stops six wrong images from being published; finding an
 * image for a work nobody has photographed can take an evening. Doing the cheap
 * pass first is what makes the corpus clean before it is shown.
 */
final readonly class ReviewQueue
{
    public function __construct(
        private ContentRepository $content,
        private SchemaRegistry $schemas,
        private Visuals $visuals,
    ) {}

    /**
     * @param bool $missing only the works without an image
     * @param bool $all include the ones already sorted and described
     * @return ReviewItem[]
     */
    public function build(?string $only = null, bool $missing = false, bool $all = false): array
    {
        $toSort = [];
        $toIllustrate = [];

        foreach ($this->content->sources() as $entity) {
            if ($this->schemas->get($entity->type)?->field('visuel') === null) {
                continue;
            }

            if ($only !== null && $entity->id !== $only) {
                continue;
            }

            $visual = $this->visuals->of($entity);

            if ($visual !== null) {
                if (! $missing && ($all || $visual->alt === null)) {
                    $toSort[] = new ReviewItem($entity, $visual);
                }

                continue;
            }

            if ($all || ! $this->visuals->declined($entity)) {
                $toIllustrate[] = new ReviewItem($entity, null);
            }
        }

        usort($toSort, $this->byTitle(...));
        usort($toIllustrate, $this->byTitle(...));

        return [...$toSort, ...$toIllustrate];
    }

    private function byTitle(ReviewItem $a, ReviewItem $b): int
    {
        return XmlSource::byTitle($a->entity, $b->entity);
    }
}
