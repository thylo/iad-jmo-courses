<?php

declare(strict_types=1);

namespace App\Media\Screen;

use App\Media\Images;

/**
 * A browser page the review command drives, in place of a preview window.
 *
 * One tab, opened once, updated as the sorting advances — rather than sixty
 * windows piling up. The socket serves two things: the page itself over plain
 * HTTP, and the frames over a WebSocket on the same port. The image travels
 * inline as a data URI, so the page never reads the disk and there is no second
 * protocol to serve files with.
 *
 * The server is pumped rather than looped: the command spends its life blocked
 * on the terminal waiting for an answer, so connections are accepted at the
 * moment a frame is sent. A browser that connects in between waits in the
 * backlog and is served at the next frame — which is why the page retries on
 * its own and why the last frame is kept and replayed to whoever arrives late.
 */
final class ReviewScreen
{
    private const string HOST = '127.0.0.1';

    /** A handful of ports, so two sessions can run side by side. */
    private const array PORTS = [8420, 8421, 8422, 8423];

    private const string GUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    /** More than this many stale tabs is a leak, not a use case. */
    private const int MAX_CLIENTS = 4;

    /** @var resource|null */
    private $server = null;

    /** @var resource[] */
    private array $clients = [];

    /** Replayed to any page that connects after the fact. */
    private ?string $last = null;

    private ?string $url = null;

    public function __construct(
        private readonly Images $images,
    ) {}

    /** @return string|null the address to look at, null when no port was free */
    public function start(): ?string
    {
        foreach (self::PORTS as $port) {
            $server = @stream_socket_server(sprintf('tcp://%s:%d', self::HOST, $port), $errno, $error);

            if ($server === false) {
                continue;
            }

            $this->server = $server;
            $this->url = sprintf('http://%s:%d/', self::HOST, $port);

            stream_set_blocking($server, false);
            $this->launch($this->url);
            $this->pump(2.0);

            return $this->url;
        }

        return null;
    }

    public function show(ScreenFrame $frame): void
    {
        $this->send([
            'position' => $frame->position,
            'total' => $frame->total,
            'title' => $frame->title,
            'facts' => $frame->facts,
            'summary' => $frame->summary,
            'doubts' => $frame->doubts,
            'caption' => $frame->caption,
            'source' => $frame->source,
            'image' => $frame->imagePath !== null && is_file($frame->imagePath)
                ? $this->images->inline($frame->imagePath)
                : null,
        ]);
    }

    /** The page says so rather than staying on the last work as if nothing happened. */
    public function stop(): void
    {
        if ($this->server === null) {
            return;
        }

        $this->send(['done' => true]);

        foreach ($this->clients as $client) {
            @fclose($client);
        }

        @fclose($this->server);

        $this->clients = [];
        $this->server = null;
    }

    /** @param array<string, mixed> $payload */
    private function send(array $payload): void
    {
        if ($this->server === null) {
            return;
        }

        $this->last = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';

        // Long enough to pick up a tab that was reloading, short enough not to
        // be felt between two questions.
        $this->pump($this->clients === [] ? 1.0 : 0.05);
        $this->broadcast($this->last);
    }

    /** Accepts and answers whatever is waiting, for at most this many seconds. */
    private function pump(float $seconds): void
    {
        $deadline = microtime(true) + $seconds;

        do {
            $client = @stream_socket_accept($this->server, 0.05);

            if ($client !== false) {
                $this->greet($client);
            }
        } while (microtime(true) < $deadline);
    }

    /** @param resource $client */
    private function greet($client): void
    {
        stream_set_timeout($client, 0, 300_000);
        $request = '';

        while (! str_contains($request, "\r\n\r\n") && strlen($request) < 8192) {
            $chunk = fread($client, 4096);

            if ($chunk === false || $chunk === '') {
                break;
            }

            $request .= $chunk;
        }

        if (preg_match('~Sec-WebSocket-Key:\s*(\S+)~i', $request, $key) !== 1) {
            $this->servePage($client);

            return;
        }

        fwrite($client, implode("\r\n", [
            'HTTP/1.1 101 Switching Protocols',
            'Upgrade: websocket',
            'Connection: Upgrade',
            'Sec-WebSocket-Accept: ' . base64_encode(sha1($key[1] . self::GUID, true)),
            '',
            '',
        ]));

        $this->clients[] = $client;

        if (count($this->clients) > self::MAX_CLIENTS) {
            @fclose(array_shift($this->clients));
        }

        if ($this->last !== null) {
            $this->write($client, $this->last);
        }
    }

    /** @param resource $client */
    private function servePage($client): void
    {
        $page = $this->page();

        fwrite($client, implode("\r\n", [
            'HTTP/1.1 200 OK',
            'Content-Type: text/html; charset=utf-8',
            'Content-Length: ' . strlen($page),
            'Connection: close',
            '',
            $page,
        ]));

        fclose($client);
    }

    private function broadcast(string $payload): void
    {
        foreach ($this->clients as $index => $client) {
            if (! $this->write($client, $payload)) {
                @fclose($client);
                unset($this->clients[$index]);
            }
        }

        $this->clients = array_values($this->clients);
    }

    /** @param resource $client */
    private function write($client, string $payload): bool
    {
        if (feof($client)) {
            return false;
        }

        return @fwrite($client, $this->frame($payload)) !== false;
    }

    /** A text frame, from the server, so never masked. */
    private function frame(string $payload): string
    {
        $length = strlen($payload);

        $header = match (true) {
            $length < 126 => chr(0x81) . chr($length),
            $length < 65536 => chr(0x81) . chr(126) . pack('n', $length),
            default => chr(0x81) . chr(127) . pack('J', $length),
        };

        return $header . $payload;
    }

    private function page(): string
    {
        return str_replace(
            '{{url}}',
            sprintf('ws://%s/', parse_url((string) $this->url, PHP_URL_HOST) . ':' . parse_url((string) $this->url, PHP_URL_PORT)),
            (string) file_get_contents(__DIR__ . '/screen.html'),
        );
    }

    private function launch(string $url): void
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            exec(sprintf('open %s > /dev/null 2>&1', escapeshellarg($url)));
        }
    }
}
