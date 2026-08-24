<?php

declare(strict_types=1);

namespace App\View;

use Tempest\View\IsView;
use Tempest\View\View;

use function Tempest\root_path;

/**
 * La page qui répond quand l'adresse ne mène à rien.
 *
 * Without it, Tempest answers a 404 with its own error page: English, dark, and
 * pulling Tailwind from a CDN. A visitor who mistypes an address should still
 * land on this site, with its sections above and its colophon below.
 */
final class NotFoundView implements View, HasNavigation
{
    use IsView;

    /**
     * The address that failed. Nothing in the tree matches it — that is why we
     * are here — so the masthead marks no section, which is correct.
     */
    public string $currentSlug {
        get => $this->requested;
    }

    public function __construct(
        private readonly string $requested,
    ) {
        $this->path = root_path('views/not-found.view.php');

        $this->data(title: 'Page introuvable');
    }
}
