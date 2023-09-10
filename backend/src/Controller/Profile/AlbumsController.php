<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractCrudController;
use App\Entity\Album;
use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext;

final class AlbumsController extends AbstractCrudController
{
    protected string $entityClass = Album::class;
    protected string $resourceRouteName = 'api:profile:artist:album';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $artist = $this->getArtist($request);

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Album::class, 'e')
            ->where('e.artist = :artist')
            ->setParameter('artist', $artist);

        return $this->listPaginatedFromQuery($request, $response, $qb->getQuery());
    }

    protected function viewRecord(object $record, ServerRequest $request): array
    {
        if (!($record instanceof Artist)) {
            throw new \InvalidArgumentException(
                sprintf('Record must be an instance of %s.', Artist::class)
            );
        }

        $router = $request->getRouter();

        $returnArray = $this->toArray($record);
        $returnArray['links'] = [
            'self' => $router->fromHere(
                routeName: $this->resourceRouteName,
                routeParams: [
                    'id' => $record->getIdRequired(),
                ],
            ),
        ];

        return $returnArray;
    }

    protected function createRecord(ServerRequest $request, array $data): object
    {
        $artist = $this->getArtist($request);

        return $this->editRecord(
            $data,
            new Album($artist)
        );
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        $artist = $this->getArtist($request);

        /** @var string $id */
        $id = $params['id'];

        return $this->em->getRepository($this->entityClass)->findOneBy([
            'id' => $id,
            'artist' => $artist,
        ]);
    }

    protected function getArtist(ServerRequest $request): Artist
    {
        $artistId = RouteContext::fromRequest($request)->getRoute()?->getArgument('artist_id');
        $artist = $this->em->find(Artist::class, $artistId);

        if (!($artist instanceof Artist)) {
            throw new \InvalidArgumentException('Artist not found!');
        }

        return $artist;
    }
}
