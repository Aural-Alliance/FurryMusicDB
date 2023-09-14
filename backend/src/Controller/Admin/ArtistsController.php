<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Profile\ArtistsController as ProfileArtistsController;
use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ArtistsController extends ProfileArtistsController
{
    protected string $entityClass = Artist::class;
    protected string $resourceRouteName = 'api:admin:artist';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Artist::class, 'e');

        return $this->listPaginatedFromQuery($request, $response, $qb->getQuery());
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        /** @var string $id */
        $id = $params['id'];

        return $this->em->find($this->entityClass, $id);
    }
}
