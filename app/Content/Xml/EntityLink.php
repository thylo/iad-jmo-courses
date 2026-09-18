<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\View\Component;

/** The one place that turns an entity id into an anchor. */
final readonly class EntityLink
{
    public function __construct(
        private Component $components,
    ) {}

    public function html(ContentIndex $index, string $id, ?string $label = null): string
    {
        $target = $index->find($id);

        return $this->components->render(
            'x-link',
            // Unresolved is not fatal: you write before you create the target.
            href: $target?->slug,
            label: $label ?? $target?->title ?? $id,
        );
    }
}
