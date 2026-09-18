<?php

declare(strict_types=1);

namespace App\Console;

use App\Content\ContentRepository;
use App\Content\Html;
use App\Content\Xml\XmlSource;
use App\Media\Images;
use App\Media\ReviewQueue;
use App\Media\Screen\ReviewScreen;
use App\Media\Screen\ScreenFrame;
use App\Media\Visual;
use App\Media\VisualDoubts;
use App\Media\Visuals;
use Tempest\Console\ConsoleArgument;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\HasConsole;

/**
 * Goes through the works one by one and asks for what a machine cannot decide.
 *
 * This replaces the contact sheet the plan called for. A web page would have
 * shown the 62 images side by side, and then the sorting would still have had
 * to happen somewhere — here the looking and the deciding are the same gesture:
 * the image opens, you keep it or you do not, you write the sentence that
 * describes it, and the fiche is updated before the next one appears.
 *
 * Nothing is remembered between runs but the content itself, so quitting after
 * five works and coming back tomorrow picks up exactly where it stopped.
 */
final readonly class MediaReviewCommand
{
    use HasConsole;

    public function __construct(
        private ReviewQueue $queue,
        private ContentRepository $content,
        private Visuals $visuals,
        private VisualDoubts $doubts,
        private ReviewScreen $screen,
        private Images $images,
    ) {}

    #[ConsoleCommand(name: 'media:review', description: 'Passe les œuvres en revue : trie les images, demande ce qui manque')]
    public function __invoke(
        #[ConsoleArgument(description: 'Une seule entité, par son id')]
        ?string $only = null,
        #[ConsoleArgument(description: 'S\'arrête après N œuvres')]
        ?int $limit = null,
        #[ConsoleArgument(description: 'Seulement celles qui n\'ont pas d\'image')]
        bool $missing = false,
        #[ConsoleArgument(description: 'Rouvre aussi celles qui sont déjà triées et décrites')]
        bool $all = false,
        #[ConsoleArgument(description: 'Seulement les images douteuses : doublons, redirections, trop petites')]
        bool $doubtful = false,
        #[ConsoleArgument(description: 'Ouvre la page de revue dans le navigateur (--no-preview pour s\'en passer)')]
        bool $preview = true,
    ): ExitCode {
        $doubts = $this->doubts->all();
        $items = $this->queue->build(only: $only, missing: $missing, all: $all || $doubtful);

        if ($doubtful) {
            $items = array_values(array_filter(
                $items,
                static fn ($item): bool => isset($doubts[$item->entity->id]),
            ));
        }

        if ($items === []) {
            $this->console->success('Rien à trier.');

            return ExitCode::SUCCESS;
        }

        if ($limit !== null) {
            $items = array_slice($items, 0, $limit);
        }

        $this->console->header('Revue des visuels', sprintf(
            '%d à voir. Chaque réponse est écrite tout de suite, on peut s\'arrêter quand on veut.',
            count($items),
        ));

        $preview = $preview && $this->openScreen();
        $done = 0;

        try {
            foreach ($items as $index => $item) {
                $reasons = $doubts[$item->entity->id] ?? [];

                $this->describeWork($item->entity, $index + 1, count($items), $reasons);

                if (! $this->treat($item->entity, $index + 1, count($items), $reasons, $preview)) {
                    break;
                }

                $done++;
            }
        } finally {
            $this->screen->stop();
        }

        $this->console->writeln();
        $this->console->info(sprintf('%d œuvre%s vue%s.', $done, $done > 1 ? 's' : '', $done > 1 ? 's' : ''));

        return ExitCode::SUCCESS;
    }

    /** The page lives in the browser; the questions stay in the terminal. */
    private function openScreen(): bool
    {
        $url = $this->screen->start();

        if ($url === null) {
            $this->console->warning('Aucun port libre pour la page de revue : les images ne s\'afficheront pas.');

            return false;
        }

        $this->console->writeln(sprintf(' Écran : <em>%s</em>', $url));

        return true;
    }

    /** What we know about the work, so the decision is not taken on a title alone. */
    /** @param string[] $doubts */
    private function describeWork(XmlSource $entity, int $position, int $total, array $doubts): void
    {
        $this->console->writeln();
        $this->console->writeln(sprintf('<style="bold fg-blue">%d/%d</style> <style="bold">%s</style>', $position, $total, $entity->title));

        foreach ($this->facts($entity) as $label => $value) {
            $this->console->keyValue($label, (string) $value);
        }

        if ($entity->summary !== null) {
            $this->console->writeln(sprintf(' <style="dim">%s</style>', $this->shorten($entity->summary)));
        }

        foreach ($doubts as $doubt) {
            $this->console->writeln(sprintf(' <style="fg-yellow">À vérifier</style> — %s', $doubt));
        }
    }

    /**
     * What the terminal and the page both show about a work.
     *
     * @return array<string, string>
     */
    private function facts(XmlSource $entity): array
    {
        return array_filter([
            'Année' => $entity->value('annee'),
            'Par' => implode(', ', $this->creators($entity)) ?: null,
            'En ligne' => $entity->value('url'),
            'Statut' => Html::attribute($entity->root, 'statut'),
        ]);
    }

    /**
     * A <par ref="studio-moniker"/> is a link, not a name. Showing the id would
     * make the reader translate it back.
     *
     * @return string[]
     */
    private function creators(XmlSource $entity): array
    {
        $index = $this->content->index();

        return array_map(
            static fn (string $id): string => $index->find($id)?->title ?? $id,
            $entity->values('par'),
        );
    }

    /**
     * @param string[] $doubts
     * @return bool false when the user asked to stop
     */
    private function treat(XmlSource $entity, int $position, int $total, array $doubts, bool $preview): bool
    {
        while (true) {
            $visual = $this->visuals->of($entity);

            if ($visual !== null) {
                $this->showImage($entity, $visual, $this->visuals->path($entity, $visual));
            }

            if ($preview) {
                $this->screen->show($this->frame($entity, $visual, $position, $total, $doubts));
            }

            $choice = $this->console->ask('Que faire ?', $this->choices($visual !== null));

            if (! is_string($choice)) {
                return false;
            }

            switch ($choice) {
                case 'garder':
                    $this->describeImage($entity, $visual);

                    return true;

                case 'rejeter':
                    $this->visuals->drop($entity);
                    $this->console->writeln(' <style="fg-red">Image retirée</style> — le fichier est supprimé, la fiche aussi.');

                    break;

                case 'fichier':
                case 'url':
                    $this->adopt($entity, $choice);

                    break;

                case 'aucune':
                    $this->visuals->decline($entity);
                    $this->console->writeln(' Notée sans image. Elle ne sera plus proposée.');

                    return true;

                case 'passer':
                    return true;

                default:
                    return false;
            }
        }
    }

    /** @return array<string, string> */
    private function choices(bool $illustrated): array
    {
        if ($illustrated) {
            return [
                'garder' => 'Garder cette image',
                'rejeter' => 'Rejeter : ce n\'est pas l\'œuvre',
                'fichier' => 'Remplacer par un fichier local',
                'url' => 'Remplacer par une adresse',
                'passer' => 'Passer, décider plus tard',
                'quitter' => 'Arrêter là',
            ];
        }

        return [
            'fichier' => 'Donner un fichier local',
            'url' => 'Donner l\'adresse d\'une image',
            'aucune' => 'Aucune image, et c\'est très bien ainsi',
            'passer' => 'Passer, décider plus tard',
            'quitter' => 'Arrêter là',
        ];
    }

    private function showImage(XmlSource $entity, Visual $visual, string $path): void
    {
        $size = is_file($path) ? $this->images->dimensions($path) : null;

        $this->console->keyValue('Image', sprintf(
            '%s%s',
            'media/' . Visual::directory($entity->type) . '/' . $visual->file,
            $size !== null ? sprintf(' (%d×%d)', $size[0], $size[1]) : ' — fichier absent',
        ));

        if ($visual->source !== null) {
            $this->console->keyValue('Vient de', $visual->source);
        }
    }

    private function adopt(XmlSource $entity, string $how): void
    {
        $source = $entity->value('url') ?? '';

        if ($how === 'fichier') {
            $path = trim((string) $this->console->ask('Chemin du fichier', hint: 'Glisser l\'image dans le terminal marche aussi.'));

            if ($path === '') {
                return;
            }

            $origin = trim((string) $this->console->ask('D\'où vient-elle ?', default: $source ?: null, hint: 'Une adresse, ou « capture d\'écran ».'));
            $failure = $this->visuals->adoptFile($entity, $path, $origin !== '' ? $origin : 'capture d\'écran');
        } else {
            $url = trim((string) $this->console->ask('Adresse de l\'image'));

            if ($url === '') {
                return;
            }

            $origin = trim((string) $this->console->ask('Page à créditer', default: $source ?: $url));
            $failure = $this->visuals->adoptUrl($entity, $url, $origin !== '' ? $origin : $url);
        }

        if ($failure !== null) {
            $this->console->error($failure);
        }
    }

    /** The two things only a person can write. Both may wait. */
    private function describeImage(XmlSource $entity, ?Visual $visual): void
    {
        if ($visual === null) {
            return;
        }

        $alt = trim((string) $this->console->ask(
            'Alternative textuelle',
            default: $visual->alt,
            hint: 'Décrire ce qu\'on voit, pas ce que c\'est. Vide : plus tard.',
        ));

        $credit = trim((string) $this->console->ask(
            'Crédit',
            default: $visual->credit,
            hint: 'Qui détient les droits. Vide : plus tard.',
        ));

        if ($alt === '' && $credit === '') {
            return;
        }

        $this->visuals->describe(
            $entity,
            alt: $alt !== '' ? $alt : null,
            credit: $credit !== '' ? $credit : null,
        );
    }

    /** @param string[] $doubts */
    private function frame(XmlSource $entity, ?Visual $visual, int $position, int $total, array $doubts): ScreenFrame
    {
        $path = $visual !== null ? $this->visuals->path($entity, $visual) : null;
        $size = $path !== null && is_file($path) ? $this->images->dimensions($path) : null;

        return new ScreenFrame(
            position: $position,
            total: $total,
            title: $entity->title,
            facts: $this->facts($entity),
            summary: $entity->summary,
            doubts: $doubts,
            imagePath: $path,
            caption: $visual !== null
                ? $visual->file . ($size !== null ? sprintf(' · %d×%d', $size[0], $size[1]) : ' · fichier absent')
                : null,
            source: $visual?->source,
        );
    }

    private function shorten(string $text, int $length = 140): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        return mb_strlen($text) <= $length ? $text : mb_substr($text, 0, $length - 1) . '…';
    }
}
