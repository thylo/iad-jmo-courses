<?php

declare(strict_types=1);

namespace App\Media\Web;

use Tempest\Container\Singleton;

/**
 * A GET, with a timeout and a size cap.
 *
 * Not Tempest\HttpClient: its interface has no room for a timeout, and a crawl
 * of 141 addresses on the open web hangs on the first host that never answers.
 * We also need the URL after redirects, to resolve a relative og:image.
 *
 * The user agent says who we are. Some hosts answer 403 to anything that is not
 * a browser — NYT, Space Cowboys, Asmodée among ours — and that is their call
 * to make; those pages get captured by hand instead of being lied to.
 */
#[Singleton]
final readonly class WebClient
{
    private const string USER_AGENT = 'Mozilla/5.0 (compatible; iad-jmo-courses/1.0; cours de narration interactive, IAD)';

    private const int TIMEOUT = 15;

    /** Beyond this, it is not an illustration. */
    private const int MAX_BYTES = 12 * 1024 * 1024;

    /** Enough not to be rude, short enough to keep 141 fetches under a coffee. */
    private const int DELAY_MICROSECONDS = 200_000;

    public function get(string $url, string $accept = '*/*'): WebResponse
    {
        return $this->send($url, $accept, body: true);
    }

    /** Is it there? Asked before downloading a file we may not want. */
    public function head(string $url): WebResponse
    {
        return $this->send($url, accept: '*/*', body: false);
    }

    private function send(string $url, string $accept, bool $body): WebResponse
    {
        usleep(self::DELAY_MICROSECONDS);

        $handle = curl_init($url);

        curl_setopt_array($handle, [
            CURLOPT_NOBODY => ! $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_HTTPHEADER => ['Accept: ' . $accept, 'Accept-Language: fr,en;q=0.8'],
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => true,
            // A 200 that turns out to be a 40 Mo file is cut here rather than in memory.
            CURLOPT_NOPROGRESS => false,
            CURLOPT_PROGRESSFUNCTION => static fn ($handle, int $expected, int $received): int
                => $received > self::MAX_BYTES ? 1 : 0,
        ]);

        $contents = curl_exec($handle);
        $error = curl_errno($handle) !== 0 ? curl_error($handle) : null;

        $response = new WebResponse(
            status: (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE),
            body: is_string($contents) ? $contents : '',
            contentType: strtolower(strtok((string) curl_getinfo($handle, CURLINFO_CONTENT_TYPE), ';') ?: ''),
            url: (string) (curl_getinfo($handle, CURLINFO_EFFECTIVE_URL) ?: $url),
            error: $error,
        );

        curl_close($handle);

        return $response;
    }
}
