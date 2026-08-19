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
    private const array EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

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
                $report->record(MediaOutcome::failed($name, $exception->getMessage()));
            }
        }

        $this->write($built);

        return $report;
    }

    private function buildOne(string $name, string $path, string $hash): MediaAsset
    {
        [$width, $height] = $this->images->dimensions($path);

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

    private function variantsExist(MediaAsset $asset): bool
    {
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
