<?php

declare(strict_types=1);

namespace App\Http;

/**
 * What the reader typed in the address bar: ?q=rain.
 *
 * Read from the superglobal rather than injected as Tempest\Http\Request, and
 * that is deliberate. The same pages are rendered outside HTTP — content:check
 * walks every document to find its dead links — and there is no request to
 * inject there. A block that could only be built while answering a request
 * would take the checker down with it, which is a heavy price for a search box.
 *
 * Everything that comes through here is a stranger's text: it is displayed by
 * templates, which escape, and compared against content, never interpreted.
 */
final readonly class QueryString
{
    public function get(string $key): string
    {
        $value = $_GET[$key] ?? null;

        // An array arrives whenever someone writes ?q[]=…, which is not a
        // search term. Nothing to report: the index simply shows everything.
        return is_string($value) ? trim($value) : '';
    }
}
