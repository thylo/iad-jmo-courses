<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/** <video src="youtube:ID" caption="…"/> — an embed without a third-party script. */
final readonly class VideoElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $source = Html::attribute($element, 'src');
        $caption = Html::attribute($element, 'caption');

        if ($source === '') {
            return '';
        }

        return $this->components->render(
            'x-video',
            src: $this->embedUrl($source),
            title: $caption !== '' ? $caption : 'Vidéo',
            caption: $caption !== '' ? $caption : null,
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
