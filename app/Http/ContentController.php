<?php

declare(strict_types=1);

namespace App\Http;

use App\Content\ContentRepository;
use Tempest\Http\Response;
use Tempest\Http\Responses\NotFound;
use Tempest\Router\Get;
use Tempest\Router\Stateless;
use Tempest\View\View;

/**
 * Route unique : toute URL est un chemin dans content/.
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

        if ($document === null) {
            return new NotFound();
        }

        return new DocumentView($document, $this->content);
    }
}
