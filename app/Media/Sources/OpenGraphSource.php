<?php

declare(strict_types=1);

namespace App\Media\Sources;

use App\Content\Xml\XmlSource;
use App\Media\Web\WebClient;

/**
 * The image a site publishes about itself: og:image, then twitter:image.
 *
 * Measured on the corpus: 80 of the 120 works with a <url> expose one, and
 * roughly 63 of those actually show the work. It is not a fallback, it is the
 * first source — and often the right image, chosen by the authors to stand for
 * their own work.
 *
 * It also gets three things wrong in ways only a human sees: an expired domain
 * showing a registrar ad, a publisher logo, a page that has nothing to do with
 * the work. That is what the sorting pass is for.
 */
final readonly class OpenGraphSource implements VisualSource
{
    private const array PROPERTIES = ['og:image', 'og:image:url', 'twitter:image', 'twitter:image:src'];

    public function __construct(
        private WebClient $web,
    ) {}

    public function name(): string
    {
        return 'og:image';
    }

    public function look(XmlSource $entity): Attempt
    {
        $url = $entity->value('url');

        if ($url === null) {
            return Attempt::nothing($this->name(), 'pas d\'url');
        }

        $response = $this->web->get($url, accept: 'text/html,application/xhtml+xml');

        if (! $response->ok()) {
            return Attempt::nothing($this->name(), $response->note());
        }

        $image = $this->extract($response->body);

        if ($image === null) {
            return Attempt::nothing($this->name(), 'pas de balise');
        }

        return Attempt::found($this->name(), $this->absolute($image, $response->url), $response->url);
    }

    /** Read with a regex on purpose: half of these pages would not survive a real parser. */
    private function extract(string $html): ?string
    {
        foreach (self::PROPERTIES as $property) {
            $pattern = sprintf(
                '~<meta[^>]+(?:property|name)\s*=\s*["\']%s["\'][^>]*>~i',
                preg_quote($property, '~'),
            );

            if (preg_match($pattern, $html, $tag) !== 1) {
                continue;
            }

            if (preg_match('~content\s*=\s*["\']([^"\']+)["\']~i', $tag[0], $content) !== 1) {
                continue;
            }

            $value = trim(html_entity_decode($content[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /** A relative og:image resolves against the page that carried it, redirects included. */
    private function absolute(string $image, string $page): string
    {
        if (preg_match('~^https?://~i', $image) === 1) {
            return $image;
        }

        $parts = parse_url($page);
        $base = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');

        if (str_starts_with($image, '//')) {
            return ($parts['scheme'] ?? 'https') . ':' . $image;
        }

        if (str_starts_with($image, '/')) {
            return $base . $image;
        }

        return $base . rtrim(dirname($parts['path'] ?? '/'), '/') . '/' . $image;
    }
}
