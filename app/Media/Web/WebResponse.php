<?php

declare(strict_types=1);

namespace App\Media\Web;

/** One answer from the open web: what came back, and what to say when nothing did. */
final readonly class WebResponse
{
    public function __construct(
        public int $status,
        public string $body,
        public string $contentType,
        /** After redirects, so a relative og:image resolves against the right page. */
        public string $url,
        public ?string $error = null,
    ) {}

    public function ok(): bool
    {
        return $this->error === null && $this->status >= 200 && $this->status < 300;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->contentType, 'image/');
    }

    /** Short reason, for the report: "HTTP 403", "timeout". */
    public function note(): string
    {
        if ($this->error !== null) {
            return $this->error;
        }

        return sprintf('HTTP %d', $this->status);
    }
}
