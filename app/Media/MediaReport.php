<?php

declare(strict_types=1);

namespace App\Media;

/**
 * What a media job did, as data rather than as printed lines.
 *
 * The jobs never write to the console: they fill a report and, if someone is
 * watching, hand each outcome to a callback as it happens. That is what lets
 * the same method answer a console command today and a form later.
 */
final class MediaReport
{
    /** @var MediaOutcome[] */
    private array $outcomes = [];

    public function __construct(
        /** Called with each outcome as it happens, for live progress. */
        private readonly ?\Closure $onProgress = null,
    ) {}

    public function record(MediaOutcome $outcome): void
    {
        $this->outcomes[] = $outcome;

        if ($this->onProgress !== null) {
            ($this->onProgress)($outcome);
        }
    }

    /** @return MediaOutcome[] */
    public function all(): array
    {
        return $this->outcomes;
    }

    /** @return MediaOutcome[] */
    public function of(MediaStatus $status): array
    {
        return array_values(array_filter(
            $this->outcomes,
            static fn (MediaOutcome $outcome): bool => $outcome->status === $status,
        ));
    }

    public function count(MediaStatus $status): int
    {
        return count($this->of($status));
    }

    public function failed(): bool
    {
        return $this->count(MediaStatus::Failed) > 0;
    }

    /** "74 écrits, 6 échecs, 61 inchangés" */
    public function summary(): string
    {
        $parts = [];

        foreach (MediaStatus::cases() as $status) {
            $count = $this->count($status);

            if ($count > 0) {
                $parts[] = sprintf('%d %s%s', $count, $status->value, $count > 1 ? 's' : '');
            }
        }

        return $parts === [] ? 'rien à faire' : implode(', ', $parts);
    }
}
