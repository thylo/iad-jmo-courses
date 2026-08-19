<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\ContentRepository;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;
use App\Media\Sources\Attempt;
use App\Media\Sources\VisualSources;

/**
 * Finds one image per entity that has none — the job behind media:fetch.
 *
 * Sources are tried in order until one answers. The file lands in media/, and
 * the fiche gets a <visuel> with its source filled in: that much is verifiable.
 * The alt text, the credit, and the decision that an image is wrong are left to
 * a human, because a machine cannot tell a work from a registrar's ad.
 *
 * Nothing here talks to a console. It fills a MediaReport and, when someone is
 * watching, hands over each outcome as it happens — which is what lets a form
 * call the same method later.
 */
final readonly class VisualFetcher
{
    public function __construct(
        private ContentRepository $content,
        private SchemaRegistry $schemas,
        private VisualSources $sources,
        private Visuals $visuals,
    ) {}

    public function fetch(
        /** Restrict to one entity id, to try a single fiche. */
        ?string $only = null,
        /** Stop after this many entities treated. */
        ?int $limit = null,
        /** Look again at entities that already have a <visuel>. */
        bool $force = false,
        /** Report what would happen without writing anything. */
        bool $dry = false,
        ?\Closure $onProgress = null,
    ): MediaReport {
        $report = new MediaReport($onProgress);
        $treated = 0;

        foreach ($this->candidates($only) as $entity) {
            if ($limit !== null && $treated >= $limit) {
                break;
            }

            if (! $force && $this->visuals->of($entity) !== null) {
                $report->record(MediaOutcome::skipped($entity->id, 'déjà illustrée'));

                continue;
            }

            // A work someone has decided to leave without an image is not a hole
            // to fill; only --force looks at it again.
            if (! $force && $this->visuals->declined($entity)) {
                $report->record(MediaOutcome::skipped($entity->id, 'sans image, décidé'));

                continue;
            }

            $treated++;
            $report->record($this->fetchOne($entity, $dry));
        }

        return $report;
    }

    private function fetchOne(XmlSource $entity, bool $dry): MediaOutcome
    {
        $notes = [];

        foreach ($this->sources->all() as $source) {
            $attempt = $source->look($entity);

            if ($attempt->candidate === null) {
                $notes[] = sprintf('%s : %s', $attempt->via, $attempt->note);

                continue;
            }

            $failure = $this->download($entity, $attempt, $dry);

            if ($failure === null) {
                return MediaOutcome::written($entity->id, $attempt->via . ' — ' . $attempt->candidate->page);
            }

            $notes[] = sprintf('%s : %s', $attempt->via, $failure);
        }

        return MediaOutcome::failed($entity->id, implode(' · ', $notes));
    }

    /**
     * A dry run downloads and checks the image all the same; it just keeps
     * nothing. "Would write" has to mean the file really came down.
     *
     * @return string|null the reason it did not work, null when it did
     */
    private function download(XmlSource $entity, Attempt $attempt, bool $dry): ?string
    {
        return $this->visuals->adoptUrl(
            $entity,
            $attempt->candidate->imageUrl,
            $attempt->candidate->page,
            dry: $dry,
        );
    }

    /**
     * The entities whose type has a <visuel>, in the order of the corpus.
     *
     * @return XmlSource[]
     */
    private function candidates(?string $only): array
    {
        $candidates = [];

        foreach ($this->content->sources() as $entity) {
            if ($this->schemas->get($entity->type)?->field('visuel') === null) {
                continue;
            }

            if ($only !== null && $entity->id !== $only) {
                continue;
            }

            $candidates[] = $entity;
        }

        usort($candidates, XmlSource::byTitle(...));

        return $candidates;
    }
}
