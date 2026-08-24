<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\View\Component;

/** The one place that turns an outside URL into an anchor. */
final readonly class ExternalLink
{
    public function __construct(
        private Component $components,
    ) {}

    public function html(string $url): string
    {
        return $this->components->render('x-external-link', url: $url);
    }
}
