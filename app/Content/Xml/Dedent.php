<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * Strips the XML indentation from a block of text.
 *
 * Shared by every element whose content is a document in another language:
 * markdown nested two levels deep would otherwise read as an indented code
 * block, and a sketch would run with its indentation shifted by the depth of
 * the tag it sits in.
 */
final class Dedent
{
    public static function text(string $source): string
    {
        $lines = preg_split('/\R/', $source) ?: [];

        while ($lines !== [] && trim($lines[0]) === '') {
            array_shift($lines);
        }

        while ($lines !== [] && trim(end($lines)) === '') {
            array_pop($lines);
        }

        $indent = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $width = strlen($line) - strlen(ltrim($line, " \t"));
            $indent = $indent === null ? $width : min($indent, $width);
        }

        if ($indent === null || $indent === 0) {
            return implode("\n", $lines);
        }

        return implode("\n", array_map(
            static fn (string $line): string => trim($line) === '' ? '' : substr($line, $indent),
            $lines,
        ));
    }
}
