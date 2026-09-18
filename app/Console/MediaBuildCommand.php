<?php

declare(strict_types=1);

namespace App\Console;

use App\Media\MediaBuilder;
use App\Media\MediaOutcome;
use App\Media\MediaReport;
use App\Media\MediaStatus;
use Tempest\Console\ConsoleArgument;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Turns the originals in media/ into the WebP variants the pages serve.
 *
 * Goes next to `npm run build` in a deployment. All the work is in MediaBuilder,
 * so the same run can be triggered from somewhere other than a terminal.
 */
final readonly class MediaBuildCommand
{
    use HasConsole;

    public function __construct(
        private MediaBuilder $builder,
    ) {}

    #[ConsoleCommand(name: 'media:build', description: 'Fabrique les variantes WebP et le manifeste des images')]
    public function __invoke(
        #[ConsoleArgument(description: 'Réencode tout, même ce qui n\'a pas changé')]
        bool $force = false,
    ): ExitCode {
        $this->console->header('Images');

        $report = $this->builder->build(
            force: $force,
            onProgress: fn (MediaOutcome $outcome) => $this->line($outcome),
        );

        return $this->conclude($report);
    }

    private function line(MediaOutcome $outcome): void
    {
        if ($outcome->status === MediaStatus::Skipped) {
            return;
        }

        $line = sprintf(' <em>%s</em> %s', str_pad($outcome->subject, 32), $outcome->detail);

        $outcome->status === MediaStatus::Failed
            ? $this->console->error($line)
            : $this->console->writeln($line);
    }

    private function conclude(MediaReport $report): ExitCode
    {
        $this->console->writeln();
        $this->console->info($report->summary());

        return $report->failed() ? ExitCode::ERROR : ExitCode::SUCCESS;
    }
}
