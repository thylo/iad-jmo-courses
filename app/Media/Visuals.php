<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Html;
use App\Content\Xml\XmlParser;
use App\Content\Xml\XmlSource;
use App\Media\Web\WebClient;

/**
 * Everything one can do to the image of a work: adopt one, drop it, describe it,
 * or say there will not be one.
 *
 * The acquisition job and the sorting command both go through here, so a file
 * arriving from the open web and a file dragged into a terminal are stored,
 * checked and recorded the same way. A form will call the same methods.
 */
final readonly class Visuals
{
    /** Below this, it is an icon or a tracking pixel, not an illustration. */
    public const int MIN_WIDTH = 320;

    public function __construct(
        private XmlParser $parser,
        private VisualWriter $writer,
        private WebClient $web,
        private Images $images,
    ) {}

    /** The image as the file says now, not as it said when the corpus was loaded. */
    public function of(XmlSource $entity): ?Visual
    {
        return Visual::of($this->parser->parse($entity->path, $entity->slug));
    }

    /** True when the fiche says this work will have no image, on purpose. */
    public function declined(XmlSource $entity): bool
    {
        return Html::attribute($this->parser->parse($entity->path, $entity->slug)->root, 'visuel') === 'aucun';
    }

    public function path(XmlSource $entity, Visual $visual): string
    {
        return MediaLibrary::sourcePath(Visual::directory($entity->type), $visual->file);
    }

    /**
     * @param bool $dry download and check, but keep nothing
     * @return string|null the reason it did not work, null when it did
     */
    public function adoptUrl(XmlSource $entity, string $imageUrl, string $source, bool $dry = false): ?string
    {
        $response = $this->web->get($imageUrl, accept: 'image/*');

        if (! $response->ok()) {
            return $response->note();
        }

        if (! $response->isImage() || $response->contentType === 'image/svg+xml') {
            return 'pas une image (' . ($response->contentType ?: 'type inconnu') . ')';
        }

        return $this->store($entity, $response->body, $source, $dry);
    }

    /** @return string|null the reason it did not work, null when it did */
    public function adoptFile(XmlSource $entity, string $path, string $source): ?string
    {
        $path = $this->expand($path);

        if (! is_file($path)) {
            return sprintf('fichier introuvable : %s', $path);
        }

        return $this->store($entity, (string) file_get_contents($path), $source);
    }

    /** Sets what only a human can write. A null value leaves the current one alone. */
    public function describe(XmlSource $entity, ?string $alt = null, ?string $credit = null, ?string $source = null): void
    {
        $visual = $this->of($entity);

        if ($visual === null) {
            return;
        }

        $this->writer->write($entity, $visual->file, source: $source, alt: $alt, credit: $credit);
    }

    /** Removes the image from the fiche and the file from media/. */
    public function drop(XmlSource $entity): void
    {
        $visual = $this->of($entity);

        $this->writer->remove($entity);

        if ($visual !== null && is_file($this->path($entity, $visual))) {
            unlink($this->path($entity, $visual));
        }
    }

    /** No image, and that is a decision rather than an oversight. */
    public function decline(XmlSource $entity): void
    {
        $this->drop($entity);
        $this->writer->decline($entity);
    }

    /** @return string|null the reason it did not work, null when it did */
    private function store(XmlSource $entity, string $binary, string $source, bool $dry = false): ?string
    {
        $file = $entity->id . '.jpg';
        $path = MediaLibrary::sourcePath(Visual::directory($entity->type), $file);
        $target = $dry ? (string) tempnam(sys_get_temp_dir(), 'visuel') : $path;

        try {
            [$width] = $this->images->store($binary, $target);
        } catch (MediaException $exception) {
            return $exception->getMessage();
        }

        $tooSmall = $width < self::MIN_WIDTH;

        if ($dry || $tooSmall) {
            unlink($target);
        }

        if ($tooSmall) {
            return sprintf('trop petite (%dpx)', $width);
        }

        if (! $dry) {
            $this->writer->write($entity, $file, source: $source);
        }

        return null;
    }

    /** A path dragged into a terminal arrives quoted, escaped, and sometimes with a ~. */
    private function expand(string $path): string
    {
        $path = trim($path);
        $path = trim($path, "'\"");
        $path = str_replace('\ ', ' ', $path);

        if (str_starts_with($path, '~/')) {
            $path = (getenv('HOME') ?: '~') . substr($path, 1);
        }

        return $path;
    }
}
