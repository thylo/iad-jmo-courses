<?php

declare(strict_types=1);

namespace App\View;

use Tempest\View\IsView;
use Tempest\View\View;

use function Tempest\root_path;

/**
 * The page that lays the whole site out on one screen.
 *
 * It is not a file under content/ because it has nothing to say that the tree
 * does not already hold: a content page here would appear in the tree it is
 * drawing, and in the masthead as a sixth section.
 */
final class SiteMapView implements View, HasNavigation
{
    use IsView;

    /** Its address, given once: the route, the masthead and sitemap.xml read it here. */
    public const string SLUG = '/plan-du-site';

    public string $currentSlug {
        get => self::SLUG;
    }

    public function __construct()
    {
        $this->path = root_path('views/layouts/sitemap.view.php');

        $this->data(
            title: 'Plan du site',
            description: 'Toutes les pages du site.',
        );
    }
}
