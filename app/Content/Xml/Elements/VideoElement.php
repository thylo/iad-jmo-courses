<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Html;
use App\Content\Xml\RenderContext;

/** <video src="youtube:ID" legende="…"/> — an embed without a third-party script. */
final readonly class VideoElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $source = Html::attribute($element, 'src');
        $caption = Html::attribute($element, 'legende');

        if ($source === '') {
            return '';
        }

        $iframe = sprintf(
            '<iframe src="%s" title="%s" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>',
            Html::escape($this->embedUrl($source)),
            Html::escape($caption !== '' ? $caption : 'Vidéo'),
        );

        return sprintf(
            '<figure class="video">%s%s</figure>',
            $iframe,
            $caption !== '' ? sprintf('<figcaption>%s</figcaption>', Html::escape($caption)) : '',
        );
    }

    /** "youtube:ID" keeps the file readable; anything else is taken as a URL. */
    private function embedUrl(string $source): string
    {
        if (str_starts_with($source, 'youtube:')) {
            // nocookie: no tracking cookie until the visitor actually plays it.
            return 'https://www.youtube-nocookie.com/embed/' . rawurlencode(substr($source, strlen('youtube:')));
        }

        if (str_starts_with($source, 'vimeo:')) {
            return 'https://player.vimeo.com/video/' . rawurlencode(substr($source, strlen('vimeo:')));
        }

        return $source;
    }
}
