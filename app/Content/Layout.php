<?php

declare(strict_types=1);

namespace App\Content;

/**
 * The layout a page asks for: layout="home" on the root element of its file.
 *
 * A page says how it wants to be laid out; the view layer maps that to a
 * template. Almost every page says nothing and gets the ordinary one. The
 * homepage is the exception, because it carries marks — a drawn rule, a
 * portrait — that only work as an introduction to the person writing, which
 * happens once.
 *
 * Adding a layout happens here and nowhere else: SchemaRegistry validates
 * content against these names, and DocumentView maps each one to its template.
 */
enum Layout: string
{
    case Document = 'document';
    case Home = 'home';

    /** @return string[] the names a content file may write */
    public static function names(): array
    {
        return array_column(self::cases(), 'value');
    }
}
