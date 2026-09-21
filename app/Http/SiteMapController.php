<?php

declare(strict_types=1);

namespace App\Http;

use App\Content\ContentRepository;
use App\View\SiteMapView;
use Tempest\Core\AppConfig;
use Tempest\Http\ContentType;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;
use Tempest\Router\Stateless;

/**
 * The map of the site, twice: a page for readers, an XML file for crawlers.
 *
 * Both are static routes, and Tempest matches those before any route with a
 * parameter — so they are answered here even though ContentController takes
 * every path.
 */
#[Stateless]
final readonly class SiteMapController
{
    public function __construct(
        private ContentRepository $content,
        private AppConfig $app,
    ) {}

    #[Get(SiteMapView::SLUG)]
    public function page(): SiteMapView
    {
        return new SiteMapView();
    }

    /**
     * Every address the site answers, entities included.
     *
     * No <lastmod>: a file's mtime is the day the image was built, not the day
     * the page changed, and a wrong date is worse than none. No <priority> or
     * <changefreq> either — crawlers ignore both.
     */
    #[Get('/sitemap.xml')]
    public function xml(): Response
    {
        $slugs = [...array_keys($this->content->all()), SiteMapView::SLUG];
        $base = rtrim($this->app->baseUri, '/');

        $entries = array_map(
            static fn (string $slug): string => sprintf(
                "  <url><loc>%s</loc></url>\n",
                htmlspecialchars($base . $slug, ENT_XML1),
            ),
            $slugs,
        );

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            . implode('', $entries)
            . "</urlset>\n";

        return new Ok($xml)->setContentType(ContentType::XML);
    }
}
