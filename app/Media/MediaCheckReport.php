<?php

declare(strict_types=1);

namespace App\Media;

/** What MediaCheck found: four lists and the count they are measured against. */
final readonly class MediaCheckReport
{
    public function __construct(
        /** Entities whose type accepts a <visuel>, illustrated or not. */
        public int $total,
        /** @var string[] entity ids with no <visuel> at all */
        public array $withoutVisual,
        /** @var string[] entity ids whose <visuel> carries no alt */
        public array $withoutAlt,
        /** @var string[] entity ids that will have no image, on purpose */
        public array $declined,
        /** @var string[] entity ids whose file exists but has no built variants */
        public array $unbuilt,
        /** @var array<string, string> entity id => file name absent from media/ */
        public array $missing,
        /** @var array<string, string[]> entity id => drawings absent from media/diagrams/ */
        public array $missingDiagrams,
        /** @var string[] files in media/ no fiche points at */
        public array $orphans,
    ) {}

    public function illustrated(): int
    {
        return $this->total - count($this->withoutVisual) - count($this->declined);
    }

    /** An image with a description written by hand is the only finished one. */
    public function described(): int
    {
        return $this->illustrated() - count($this->withoutAlt);
    }
}
