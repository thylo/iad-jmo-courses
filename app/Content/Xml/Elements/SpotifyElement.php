<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/** <spotify id="0BgkDOzOF1liETIGYwd3IY" type="track" caption="…"/> — an embed, like <video>. */
final readonly class SpotifyElement implements ElementRenderer
{
    /** What Spotify serves under /embed/. Anything else is a typo, not a page. */
    private const array KINDS = ['track', 'album', 'playlist', 'artist', 'episode', 'show'];

    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $id = $this->identifier(Html::attribute($element, 'id'));
        $kind = $this->kind($element);
        $caption = Html::attribute($element, 'caption');

        if ($id === '' || $kind === null) {
            return '';
        }

        return $this->components->render(
            'x-spotify',
            src: 'https://open.spotify.com/embed/' . $kind . '/' . rawurlencode($id),
            title: $caption !== '' ? $caption : 'Écouter sur Spotify',
            caption: $caption !== '' ? $caption : null,
        );
    }

    /**
     * The bare id is what one writes; what one copies from Spotify is a URI or
     * a share link, so both are read too. The ?si= tracking parameter that
     * comes with the link is dropped rather than passed on.
     */
    private function identifier(string $written): string
    {
        if (str_contains($written, '/')) {
            $written = (string) parse_url($written, PHP_URL_PATH);
        }

        $parts = preg_split('#[:/]#', $written) ?: [];

        return trim((string) end($parts));
    }

    /** type= says it, and the pasted URI or link says it too when type= is silent. */
    private function kind(\Dom\Element $element): ?string
    {
        $declared = Html::attribute($element, 'type');

        if ($declared === '') {
            $written = Html::attribute($element, 'id');

            foreach (self::KINDS as $kind) {
                if (str_contains($written, $kind . ':') || str_contains($written, '/' . $kind . '/')) {
                    return $kind;
                }
            }

            return 'track';
        }

        return in_array($declared, self::KINDS, true) ? $declared : null;
    }
}
