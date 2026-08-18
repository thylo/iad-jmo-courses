<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Html;

/** The one place that turns an outside URL into an anchor. */
final class ExternalLink
{
    public static function html(string $url, ?string $label = null): string
    {
        return sprintf(
            '<a href="%s" rel="noopener noreferrer">%s</a>',
            Html::escape($url),
            Html::escape($label ?? $url),
        );
    }
}
