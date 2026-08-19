<?php

declare(strict_types=1);

namespace App\Media;

/** One entry of the manifest: an original and the variants built from it. */
final readonly class MediaAsset
{
    public function __construct(
        /** "oeuvres/unlock", the path under media/ without its extension. */
        public string $name,
        /** Dimensions of the original, so the browser can reserve the space. */
        public int $width,
        public int $height,
        /** @var int[] widths actually built, never wider than the original */
        public array $widths,
        /** Of the original, so an untouched file is not encoded twice. */
        public string $hash,
    ) {}

    public function src(int $width): string
    {
        return sprintf('/media/%s-%d.webp', $this->name, $this->closest($width));
    }

    public function srcset(): string
    {
        return implode(', ', array_map(
            fn (int $width): string => sprintf('/media/%s-%d.webp %dw', $this->name, $width, $width),
            $this->widths,
        ));
    }

    /** The intrinsic ratio, for the height attribute that goes with a width. */
    public function heightFor(int $width): int
    {
        return (int) round($width * $this->height / $this->width);
    }

    /** @param array<string, mixed> $entry */
    public static function fromArray(string $name, array $entry): self
    {
        return new self(
            name: $name,
            width: (int) $entry['width'],
            height: (int) $entry['height'],
            widths: array_map(intval(...), (array) $entry['widths']),
            hash: (string) $entry['hash'],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'widths' => $this->widths,
            'hash' => $this->hash,
        ];
    }

    /** A source narrower than the asked width has no variant at that width. */
    private function closest(int $width): int
    {
        foreach ($this->widths as $available) {
            if ($available >= $width) {
                return $available;
            }
        }

        return $this->widths[array_key_last($this->widths)] ?? $width;
    }
}
