<?php

declare(strict_types=1);

namespace App\Console;

use App\Content\ContentRepository;
use App\Content\Document;
use App\Media\MediaCheck;
use App\Media\VisualDoubts;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Content inventory, link check, and the list of entities still to be written.
 */
final readonly class ContentCheckCommand
{
    use HasConsole;

    public function __construct(
        private ContentRepository $content,
        private MediaCheck $media,
        private VisualDoubts $doubts,
    ) {}

    #[ConsoleCommand(name: 'content:check', description: 'Liste les pages, signale les liens morts et les entités à écrire')]
    public function __invoke(): ExitCode
    {
        $documents = $this->content->all();

        if ($documents === []) {
            $this->console->error('Aucun document trouvé dans content/.');

            return ExitCode::ERROR;
        }

        $this->console->header('Pages');

        foreach ($documents as $slug => $document) {
            $this->console->writeln(sprintf(' <em>%s</em>  %s', str_pad($slug, 24), $document->title));
        }

        $this->reportFailures();
        $this->reportMissingEntities();
        $this->reportImages();

        return $this->reportBrokenLinks($documents);
    }

    /**
     * Files that did not load at all.
     *
     * These are the only content errors that cost a URL, so they come first and
     * they are errors rather than warnings.
     */
    private function reportFailures(): void
    {
        $failures = $this->content->failures();

        if ($failures === []) {
            return;
        }

        $this->console->header('Fichiers illisibles');

        foreach ($failures as $message) {
            $this->console->error($message);
        }
    }

    /**
     * Unresolved [[references]] are reported but do not fail the run.
     *
     * Writing before creating the target is the normal way to work — the list is
     * a to-write queue, not a set of mistakes.
     */
    private function reportMissingEntities(): void
    {
        $index = $this->content->index();
        $missing = [];

        foreach ($this->content->sources() as $source) {
            foreach ($source->references as $reference) {
                if (! $index->has($reference)) {
                    $missing[$reference][] = $source->id;
                }
            }
        }

        if ($missing === []) {
            return;
        }

        ksort($missing);

        $this->console->header('Entités à écrire');

        foreach ($missing as $id => $citedBy) {
            $this->console->warning(sprintf('%s — cité par %s', $id, implode(', ', array_unique($citedBy))));
        }
    }

    /**
     * The images, as two queues and two errors.
     *
     * Illustrating and describing are content work that will take weeks; a file
     * named by a fiche but absent from media/, or lying in media/ without a
     * fiche, is a mistake to fix now.
     */
    private function reportImages(): void
    {
        $report = $this->media->run();

        $this->console->header('Images');

        $this->console->writeln(sprintf(
            ' %d illustrées sur %d, %d avec une alternative textuelle.%s',
            $report->illustrated(),
            $report->total,
            $report->described(),
            $report->declined !== [] ? sprintf(' %d sans image, décidé.', count($report->declined)) : '',
        ));

        // Counts, not lists: naming 78 works to illustrate on every run would
        // bury the two lines under it that are actual mistakes.
        if ($report->withoutVisual !== [] || $report->withoutAlt !== []) {
            $this->console->warning(sprintf(
                '%d à illustrer, %d alt à écrire.',
                count($report->withoutVisual),
                count($report->withoutAlt),
            ));
        }

        $doubts = $this->doubts->all();

        if ($doubts !== []) {
            $this->console->warning(sprintf(
                '%d image%s à vérifier : php ./tempest media:review --doubtful',
                count($doubts),
                count($doubts) > 1 ? 's' : '',
            ));
        }

        if ($report->unbuilt !== []) {
            $this->console->error(sprintf(
                '%d image%s pas encore fabriquée%s (elles ne s\'affichent pas) : php ./tempest media:build',
                count($report->unbuilt),
                count($report->unbuilt) > 1 ? 's' : '',
                count($report->unbuilt) > 1 ? 's' : '',
            ));
        }

        foreach ($report->missing as $id => $file) {
            $this->console->error(sprintf('%s — fichier absent de media/ : %s', $id, $file));
        }

        foreach ($report->missingDiagrams as $id => $files) {
            $this->console->error(sprintf(
                '%s — schéma absent de media/diagrams/ : %s',
                $id,
                implode(', ', $files),
            ));
        }

        foreach ($report->orphans as $path) {
            $this->console->error(sprintf('%s — plus aucune fiche ne cite ce fichier', $path));
        }
    }

    /** @param array<string, Document> $documents */
    private function reportBrokenLinks(array $documents): ExitCode
    {
        $broken = $this->brokenLinks($documents);

        $this->console->header('Liens internes');

        if ($broken === []) {
            $this->console->success(sprintf('%d pages, aucun lien mort.', count($documents)));

            return $this->content->failures() === [] ? ExitCode::SUCCESS : ExitCode::ERROR;
        }

        foreach ($broken as $slug => $targets) {
            foreach ($targets as $target) {
                $this->console->error(sprintf('%s -> %s', $slug, $target));
            }
        }

        return ExitCode::ERROR;
    }

    /**
     * @param array<string, Document> $documents
     * @return array<string, string[]> dead links, keyed by source page
     */
    private function brokenLinks(array $documents): array
    {
        $broken = [];

        foreach ($documents as $slug => $document) {
            // ~ delimiter: the pattern holds a # (anchors), which would close a # delimiter.
            preg_match_all('~<a[^>]+href="(/[^"\#]*)"~i', $document->html, $matches);

            foreach (array_unique($matches[1]) as $target) {
                $normalised = $target === '/' ? '/' : rtrim($target, '/');

                if ($this->content->find($normalised) !== null) {
                    continue;
                }

                $broken[$slug][] = $target;
            }
        }

        return $broken;
    }
}
