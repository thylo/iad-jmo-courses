<?php

declare(strict_types=1);

namespace App\Http;

use App\Content\ContentRepository;
use App\View\DocumentView;
use App\View\NotFoundView;
use Tempest\Http\Response;
use Tempest\Http\Responses\NotFound;
use Tempest\Router\Get;
use Tempest\Router\Stateless;
use Tempest\View\View;

/**
 * A single route: every URL is a path inside content/.
 */
#[Stateless]
final readonly class ContentController
{
    public function __construct(
        private ContentRepository $content,
    ) {}

    #[Get('/')]
    #[Get('/{path:.*}')]
    public function show(string $path = ''): View|Response
    {
        $document = $this->content->find($path);

        // The view matters: an empty NotFound is filled in by the framework's
        // own error page, which is not this site.
        if ($document === null) {
            return new NotFound(new NotFoundView('/' . $path));
        }

        return new DocumentView($document);
    }
}
