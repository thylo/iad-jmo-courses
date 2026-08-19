<?php

declare(strict_types=1);

namespace App\Media;

/**
 * An image is missing or unreadable.
 *
 * Same rule as ContentException: the message names the file and says what to
 * run. A half-rendered page is worse than a plain failure.
 */
final class MediaException extends \RuntimeException
{
    public static function unknownAsset(string $name): self
    {
        return new self(sprintf(
            'Image « %s » absente du manifeste. Lancer : php ./tempest media:build',
            $name,
        ));
    }

    public static function unreadable(string $path): self
    {
        return new self(sprintf('Image illisible : %s', $path));
    }
}
