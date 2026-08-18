<?php

declare(strict_types=1);

namespace App\Console;

use App\Content\ContentRepository;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Inventaire du contenu et contrôle des liens internes.
 *
 * C'est le filet dont on aura besoin au moment de migrer pour de bon : le contenu
 * Astro compte 293 liens absolus, et rien ne garantit aujourd'hui qu'ils pointent
 * tous quelque part.
 */
final readonly class ContentCheckCommand
{
    use HasConsole;

    public function __construct(
        private ContentRepository $content,
    ) {}

    #[ConsoleCommand(name: 'content:check', description: 'Liste les pages et signale les liens internes morts')]
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
     * @param array<string, \App\Content\Document> $documents
     * @return array<string, string[]> liens morts, indexés par page source
     */
    private function brokenLinks(array $documents): array
    {
        $broken = [];

        foreach ($documents as $slug => $document) {
            // Délimiteur ~ : le motif contient un # (les ancres), qui fermerait un délimiteur #.
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
