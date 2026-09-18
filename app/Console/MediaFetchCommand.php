<?php

declare(strict_types=1);

namespace App\Console;

use App\Media\MediaOutcome;
use App\Media\MediaReport;
use App\Media\MediaStatus;
use App\Media\VisualFetcher;
use Tempest\Console\ConsoleArgument;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Goes looking for one image per work that has none.
 *
 * The failures are as useful as the successes: a 404 on a fiche that claims the
 * work is online is content that has gone false, and this is where it shows up.
 */
final readonly class MediaFetchCommand
{
    use HasConsole;

    public function __construct(
        private VisualFetcher $fetcher,
    ) {}

    #[ConsoleCommand(name: 'media:fetch', description: 'Cherche une image pour chaque œuvre qui n\'en a pas')]
    public function __invoke(
        #[ConsoleArgument(description: 'Une seule entité, par son id')]
        ?string $only = null,
        #[ConsoleArgument(description: 'S\'arrête après N entités')]
        ?int $limit = null,
        #[ConsoleArgument(description: 'Reprend aussi celles qui ont déjà un visuel')]
        bool $force = false,
        #[ConsoleArgument(description: 'Télécharge et vérifie, mais n\'écrit rien')]
        bool $dry = false,
    ): ExitCode {
        $this->console->header('Visuels', $dry ? 'simulation : rien ne sera écrit' : null);

        $report = $this->fetcher->fetch(
            only: $only,
            limit: $limit,
            force: $force,
            dry: $dry,
            onProgress: fn (MediaOutcome $outcome) => $this->line($outcome),
        );

        return $this->conclude($report);
    }

    private function line(MediaOutcome $outcome): void
    {
        if ($outcome->status === MediaStatus::Skipped) {
            return;
        }

        $subject = str_pad($outcome->subject, 32);

        if ($outcome->status === MediaStatus::Failed) {
            $this->console->writeln(sprintf(' <style="fg-red">%s</style> %s', $subject, $outcome->detail));

            return;
        }

        $this->console->writeln(sprintf(' <em>%s</em> %s', $subject, $outcome->detail));
    }

    private function conclude(MediaReport $report): ExitCode
    {
        $this->console->writeln();
        $this->console->info($report->summary());

        // A work nobody publishes an image of is not a failure of the run: it is
        // the queue for the Chrome capture and the Wayback Machine.
        return ExitCode::SUCCESS;
    }
}
