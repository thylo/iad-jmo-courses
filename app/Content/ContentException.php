<?php

declare(strict_types=1);

namespace App\Content;

/**
 * A content file is invalid.
 *
 * The message always names the file and, when we have it, the line: a content
 * error must be fixable without opening the code.
 */
final class ContentException extends \RuntimeException
{
    public static function at(string $path, string $message, ?int $line = null): self
    {
        return new self($line !== null
            ? sprintf('%s:%d — %s', $path, $line, $message)
            : sprintf('%s — %s', $path, $message));
    }
}
