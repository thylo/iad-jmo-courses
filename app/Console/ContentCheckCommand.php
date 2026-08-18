<?php

declare(strict_types=1);

namespace App\Console;

use App\Content\ContentRepository;
use App\Content\Document;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Content inventory, link check, and the list of entities still to be written.
 *
 * This is the net we will need when migrating for real: the Astro content holds
 * 293 absolute links, and nothing guarantees today that they all land somewhere.
 */
final readonly class ContentCheckCommand
{
    use HasConsole;

    public function __construct(
        private ContentRepository $content,
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

        $this->reportMissingEntities();

        return $this->reportBrokenLinks($documents);
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

    /** @param array<string, Document> $documents */
    private function reportBrokenLinks(array $documents): ExitCode
    {
        $broken = $this->brokenLinks($documents);

        $this->console->header('Liens internes');

        if ($broken === []) {
            $this->console->success(sprintf('%d pages, aucun lien mort.', count($documents)));

            return ExitCode::SUCCESS;
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
