<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractCrudController;
use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Track;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext;

final class TracksController extends AbstractCrudController
{
    protected string $entityClass = Track::class;
    protected string $resourceRouteName = 'api:profile:artist:album:track';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $album = $this->getAlbum($request);

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Track::class, 'e')
            ->where('e.album = :album')
            ->setParameter('album', $album);

        return $this->listPaginatedFromQuery($request, $response, $qb->getQuery());
    }

    protected function viewRecord(object $record, ServerRequest $request): array
    {
        if (!($record instanceof Track)) {
            throw new \InvalidArgumentException(
                sprintf('Record must be an instance of %s.', Track::class)
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
        $album = $this->getAlbum($request);

        return $this->editRecord(
            $data,
            new Track($album)
        );
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        $album = $this->getAlbum($request);

        /** @var string $id */
        $id = $params['id'];

        return $this->em->getRepository($this->entityClass)->findOneBy([
            'id' => $id,
            'album' => $album,
        ]);
    }

    protected function getAlbum(ServerRequest $request): Album
    {
        $currentRoute = RouteContext::fromRequest($request)->getRoute();

        $artistId = $currentRoute?->getArgument('artist_id');
        $artist = $this->em->find(Artist::class, $artistId);

        if (!($artist instanceof Artist)) {
            throw new \InvalidArgumentException('Artist not found!');
        }

        $albumId = $currentRoute?->getArgument('album_id');
        $album = $this->em->getRepository(Album::class)->findOneBy([
            'id' => $albumId,
            'artist' => $artist,
        ]);

        if (!($album instanceof Album)) {
            throw new \InvalidArgumentException('Album not found!');
        }

        return $album;
    }
}
