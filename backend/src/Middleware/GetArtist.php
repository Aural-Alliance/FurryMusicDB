<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Artist;
use App\Http\ServerRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

final class GetArtist implements MiddlewareInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeArgs = RouteContext::fromRequest($request)->getRoute()?->getArguments();
        $id = $routeArgs['artist_id'] ?? null;

        $record = $this->em->find(Artist::class, $id);

        if (!($record instanceof Artist)) {
            throw new \InvalidArgumentException('Artist not found!');
        }

        $request = $request->withAttribute(
            ServerRequest::ATTR_ARTIST,
            $record
        );

        return $handler->handle($request);
    }
}
