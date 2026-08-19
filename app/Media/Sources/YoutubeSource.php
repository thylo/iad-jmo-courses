<?php

declare(strict_types=1);

namespace App\Media\Sources;

use App\Content\Html;
use App\Content\Xml\XmlSource;
use App\Media\Web\WebClient;

/**
 * The thumbnail of a video already named in the fiche.
 *
 * Three works have a video as their <url> — Fort McMoney, Phallaina, Prison
 * Valley — and a dozen more cite one in their prose. No search is involved: a
 * YouTube search returns two-hour playthroughs and lying trailers. Only an
 * identifier someone already wrote down is trusted.
 *
 * The same file serves twice, here and as the poster of the video facade.
 */
final readonly class YoutubeSource implements VisualSource
{
    private const string ID = '[A-Za-z0-9_-]{11}';

    public function __construct(
        private WebClient $web,
    ) {}

    public function name(): string
    {
        return 'youtube';
    }

    public function look(XmlSource $entity): Attempt
    {
        $id = $this->identifier($entity);

        if ($id === null) {
            return Attempt::nothing($this->name(), 'pas de vidéo');
        }

        return Attempt::found(
            $this->name(),
            $this->thumbnail($id),
            sprintf('https://www.youtube.com/watch?v=%s', $id),
        );
    }

    /**
     * maxresdefault only exists for videos uploaded in HD; hqdefault always
     * does, at 480×360. Asking first costs one HEAD and saves a 404.
     */
    private function thumbnail(string $id): string
    {
        $maximum = sprintf('https://i.ytimg.com/vi/%s/maxresdefault.jpg', $id);

        return $this->web->head($maximum)->ok()
            ? $maximum
            : sprintf('https://i.ytimg.com/vi/%s/hqdefault.jpg', $id);
    }

    private function identifier(XmlSource $entity): ?string
    {
        foreach ($entity->root->getElementsByTagName('video') as $video) {
            $src = Html::attribute($video, 'src');

            if (str_starts_with($src, 'youtube:')) {
                return substr($src, strlen('youtube:'));
            }
        }

        return $this->fromText($entity->value('url') ?? '')
            ?? $this->fromText($entity->root->textContent);
    }

    private function fromText(string $text): ?string
    {
        $patterns = [
            '~youtu\.be/(' . self::ID . ')~',
            '~youtube(?:-nocookie)?\.com/watch\?(?:[^"\s]*&)?v=(' . self::ID . ')~',
            '~youtube(?:-nocookie)?\.com/embed/(' . self::ID . ')~',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match) === 1) {
                return $match[1];
            }
        }

        return null;
    }
}
