<?php

declare(strict_types=1);

namespace App\Middleware\Acl;

use App\Exception\PermissionDeniedException;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class CheckCanManageArtist
{
    public function __construct(
        private string $param = 'artist_id'
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $routeParams = RouteContext::fromRequest($request);
        $param = $routeParams->getRoute()->getArgument($this->param);

        if (empty($param)) {
            throw new \InvalidArgumentException(sprintf('Param %s not found.', $this->param));
        }

        $acl = $request->getAcl();
        if (!$acl->canManageArtist($param)) {
            throw new PermissionDeniedException();
        }

        return $handler->handle($request);
    }
}
