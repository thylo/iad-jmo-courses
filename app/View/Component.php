<?php

declare(strict_types=1);

namespace App\View;

use Tempest\View\ViewConfig;
use Tempest\View\ViewRenderer;

use function Tempest\View\view;

/**
 * The one door between the content layer and a template.
 *
 * The tree walk composes strings and cannot be a view component itself —
 * Tempest expands components at compile time, so a component that renders
 * itself never terminates, which is what NodeRenderer explains. But nothing in
 * PHP writes a tag: a renderer answers what it wants shown, and a .view.php
 * file decides what that looks like.
 *
 * A component is asked for by its name, the way a template asks for it. Where
 * the file sits under views/ is read from what discovery registered, so moving
 * it to another folder touches no caller.
 */
final readonly class Component
{
    public function __construct(
        private ViewRenderer $renderer,
        private ViewConfig $config,
    ) {}

    /** @param mixed ...$data the view data, by name, as the template declares it */
    public function render(string $name, mixed ...$data): string
    {
        return $this->renderer->render(view($this->config->viewComponents[$name]->file, ...$data));
    }
}
