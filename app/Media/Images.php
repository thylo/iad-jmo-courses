<?php

declare(strict_types=1);

namespace App\Media;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Tempest\Container\Singleton;

/**
 * Decoding, scaling and encoding — the only place that knows about GD.
 *
 * Intervention comes with the framework (tempest/responsive-image depends on
 * it), so there is nothing to install. That package's own factory is not used:
 * it derives its widths from a file-size heuristic, keeps the source format and
 * builds at render time, where we want three fixed widths, WebP, and a build
 * step.
 */
#[Singleton]
final class Images
{
    /** Widest an original is kept. A screen capture beyond that is weight, not detail. */
    public const int MAX_WIDTH = 1600;

    private const int JPEG_QUALITY = 85;

    private const int WEBP_QUALITY = 78;

    private readonly ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Stores a downloaded image as an original: at most MAX_WIDTH, JPEG, no EXIF.
     *
     * A PNG capture weighs 3 Mo where the JPEG weighs 200 Ko, and nobody will
     * see the difference on a screenshot of a website.
     *
     * @return array{0: int, 1: int} the stored dimensions
     */
    public function store(string $binary, string $path): array
    {
        try {
            $image = $this->manager->decodeBinary($binary);
        } catch (\Throwable) {
            throw MediaException::unreadable($path);
        }

        $image->scaleDown(width: self::MAX_WIDTH);

        $this->makeDirectory($path);
        $image->encode(new JpegEncoder(quality: self::JPEG_QUALITY, strip: true))->save($path);

        return [$image->width(), $image->height()];
    }

    /**
     * Read from the file header when possible: measuring 62 images to sort them
     * should not decode 62 images.
     *
     * @return array{0: int, 1: int}
     */
    public function dimensions(string $path): array
    {
        $size = @getimagesize($path);

        if (is_array($size)) {
            return [$size[0], $size[1]];
        }

        $image = $this->read($path);

        return [$image->width(), $image->height()];
    }

    /** One variant, in WebP. Never wider than the source: upscaling invents pixels. */
    public function variant(string $source, string $target, int $width): void
    {
        $image = $this->read($source);
        $image->scaleDown(width: $width);

        $this->makeDirectory($target);
        $image->encode(new WebpEncoder(quality: self::WEBP_QUALITY, strip: true))->save($target);
    }

    /**
     * A copy small enough to travel down a socket, for the review screen.
     *
     * WebP at 900px is 40 to 80 Ko — sending it inline costs less than serving
     * the file over a second protocol.
     */
    public function inline(string $path, int $width = 900): string
    {
        $image = $this->read($path);
        $image->scaleDown(width: $width);

        return (string) $image->encode(new WebpEncoder(quality: 70, strip: true))->toDataUri();
    }

    private function read(string $path): \Intervention\Image\Interfaces\ImageInterface
    {
        try {
            return $this->manager->decodePath($path);
        } catch (\Throwable) {
            throw MediaException::unreadable($path);
        }
    }

    private function makeDirectory(string $path): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, recursive: true);
        }
    }
}
