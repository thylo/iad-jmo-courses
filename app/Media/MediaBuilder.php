<?php

declare(strict_types=1);

namespace App\Media;

use function Tempest\root_path;

/**
 * Builds the WebP variants and the manifest — the job behind media:build.
 *
 * Reads media/, writes public/media/. Takes a callback so a console command can
 * print as it goes and a form can do something else with the same run.
 *
 * What has not changed is not encoded again: the manifest carries the hash of
 * each original, so the first pass takes minutes and the next ones seconds.
 */
final readonly class MediaBuilder
{
    private const array EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Copied as it is, never encoded.
     *
     * GD reads one frame of an animated GIF and would hand back a still, so
     * the encoder is the wrong tool: there is nothing to scale here, only a
     * finished object to serve. A GIF that is too heavy for a page is a
     * problem to fix in the file, not in the build.
     */
    private const array VERBATIM = ['gif'];

    public function __construct(
        private Images $images,
    ) {}

    public function build(bool $force = false, ?\Closure $onProgress = null): MediaReport
    {
        $report = new MediaReport($onProgress);
        $known = $this->manifest();
        $built = [];

        foreach ($this->originals() as $name => $path) {
            $hash = (string) md5_file($path);
            $existing = $known[$name] ?? null;

            if (! $force && $existing !== null && $existing->hash === $hash && $this->variantsExist($existing)) {
                $built[$name] = $existing;
                $report->record(MediaOutcome::skipped($name));

                continue;
            }

            try {
                $built[$name] = $this->buildOne($name, $path, $hash);
                $report->record(MediaOutcome::written($name, implode('/', $built[$name]->widths)));
            } catch (MediaException $exception) {
                // The previous variants are still on disk and still correct, so
                // the entry stays: a failed encode must not take the image off
                // every page that uses it. Its hash stays the old one, which is
                // what makes the next run try again.
                if ($existing !== null && $this->variantsExist($existing)) {
                    $built[$name] = $existing;
                }

                $report->record(MediaOutcome::failed($name, $exception->getMessage()));
            }
        }

        $this->write($built);

        return $report;
    }

    private function buildOne(string $name, string $path, string $hash): MediaAsset
    {
        [$width, $height] = $this->images->dimensions($path);
        $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($format, self::VERBATIM, true)) {
            $this->copy($path, $this->verbatimPath($name, $format));

            return new MediaAsset(
                name: $name,
                width: $width,
                height: $height,
                widths: [$width],
                hash: $hash,
                format: $format,
            );
        }

        $asset = new MediaAsset(
            name: $name,
            width: $width,
            height: $height,
            widths: $this->widthsFor($width),
            hash: $hash,
        );

        foreach ($asset->widths as $variant) {
            $this->images->variant($path, $this->variantPath($name, $variant), $variant);
        }

        return $asset;
    }

    private function copy(string $source, string $target): void
    {
        $directory = dirname($target);

        if (! is_dir($directory)) {
            mkdir($directory, recursive: true);
        }

        if (! copy($source, $target)) {
            throw MediaException::unreadable($source);
        }
    }

    /** @return int[] */
    private function widthsFor(int $width): array
    {
        $widths = array_values(array_filter(
            MediaLibrary::WIDTHS,
            static fn (int $candidate): bool => $candidate <= $width,
        ));

        // A source narrower than the smallest variant is served as it is.
        return $widths !== [] ? $widths : [$width];
    }

    /** @return array<string, string> asset name => absolute path of the original */
    private function originals(): array
    {
        $root = MediaLibrary::sourcePath();

        if (! is_dir($root)) {
            return [];
        }

        $originals = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || ! in_array(strtolower($file->getExtension()), self::EXTENSIONS, true)) {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($root) + 1);
            $originals[$this->nameOf($relative)] = $file->getPathname();
        }

        ksort($originals);

        return $originals;
    }

    /** "oeuvres/unlock.jpg" -> "oeuvres/unlock" */
    private function nameOf(string $relative): string
    {
        $directory = pathinfo($relative, PATHINFO_DIRNAME);
        $filename = pathinfo($relative, PATHINFO_FILENAME);

        return $directory === '.' ? $filename : $directory . '/' . $filename;
    }

    private function variantPath(string $name, int $width): string
    {
        return root_path('public', 'media', sprintf('%s-%d.webp', $name, $width));
    }

    private function verbatimPath(string $name, string $format): string
    {
        return root_path('public', 'media', sprintf('%s.%s', $name, $format));
    }

    private function variantsExist(MediaAsset $asset): bool
    {
        if ($asset->format !== 'webp') {
            return is_file($this->verbatimPath($asset->name, $asset->format));
        }

        foreach ($asset->widths as $width) {
            if (! is_file($this->variantPath($asset->name, $width))) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, MediaAsset> */
    private function manifest(): array
    {
        $path = MediaLibrary::manifestPath();

        if (! is_file($path)) {
            return [];
        }

        $entries = json_decode((string) file_get_contents($path), associative: true);
        $assets = [];

        foreach (is_array($entries) ? $entries : [] as $name => $entry) {
            $assets[$name] = MediaAsset::fromArray((string) $name, $entry);
        }

        return $assets;
    }

    /** @param array<string, MediaAsset> $assets */
    private function write(array $assets): void
    {
        $entries = [];

        foreach ($assets as $name => $asset) {
            $entries[$name] = $asset->toArray();
        }

        $path = MediaLibrary::manifestPath();

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), recursive: true);
        }

        file_put_contents($path, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
