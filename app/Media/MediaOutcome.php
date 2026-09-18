<?php

declare(strict_types=1);

namespace App\Media;

/** One line of a report: what was treated, what happened, and why. */
final readonly class MediaOutcome
{
    private function __construct(
        /** The entity id or the asset name. */
        public string $subject,
        public MediaStatus $status,
        public string $detail,
    ) {}

    public static function written(string $subject, string $detail = ''): self
    {
        return new self($subject, MediaStatus::Written, $detail);
    }

    public static function skipped(string $subject, string $detail = ''): self
    {
        return new self($subject, MediaStatus::Skipped, $detail);
    }

    public static function failed(string $subject, string $detail): self
    {
        return new self($subject, MediaStatus::Failed, $detail);
    }
}
