<?php

declare(strict_types=1);

namespace App\Media;

use Tempest\Container\Singleton;

use function Tempest\root_path;

/**
 * The manifest of built images, read once per request.
 *
 * The originals live in media/ and are content, versioned next to the text they
 * illustrate. The variants live in public/media/, are made by media:build, and
 * are never edited by hand — so nothing that renders has to look at a file on
 * disk, measure it, or guess its ratio.
 */
#[Singleton]
final class MediaLibrary
{
    /** Display widths, taken from the layout: a 62ch column is ~530px, the full grid ~780px. */
    public const array WIDTHS = [320, 640, 1280];

    /** @var array<string, MediaAsset>|null */
    private ?array $assets = null;

    public function has(string $name): bool
    {
        return $this->find($name) !== null;
    }

    public function find(string $name): ?MediaAsset
    {
        return $this->all()[$name] ?? null;
    }

    public function get(string $name): MediaAsset
    {
        return $this->find($name) ?? throw MediaException::unknownAsset($name);
    }

    /** @return array<string, MediaAsset> */
    public function all(): array
    {
        return $this->assets ??= $this->read();
    }

    public static function manifestPath(): string
    {
        return root_path('public', 'media', 'index.json');
    }

    /** Where the originals of a type live: media/oeuvres/. */
    public static function sourcePath(string ...$parts): string
    {
        return root_path('media', ...$parts);
    }

    /** @return array<string, MediaAsset> */
    private function read(): array
    {
        $path = self::manifestPath();

        // No manifest is not an error: it is a site that has not built its
        // images yet. Asking for one by name is what fails, and it says so.
        if (! is_file($path)) {
            return [];
        }

        $entries = json_decode((string) file_get_contents($path), associative: true);

        if (! is_array($entries)) {
            return [];
        }

        $assets = [];

        foreach ($entries as $name => $entry) {
            $assets[$name] = MediaAsset::fromArray((string) $name, $entry);
        }

        return $assets;
    }
}
