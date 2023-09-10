<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractCrudController;
use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ArtistsController extends AbstractCrudController
{
    protected string $entityClass = Artist::class;
    protected string $resourceRouteName = 'api:profile:artist';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $acl = $request->getAcl();

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Artist::class, 'e');

        if (!$acl->isAdministrator()) {
            $qb->where('e.owner = :user')
                ->setParameter('user', $request->getUser());
        }

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
                routeParams: ['id' => $record->getIdRequired()],
            ),
        ];

        return $returnArray;
    }

    protected function createRecord(ServerRequest $request, array $data): object
    {
        return $this->editRecord(
            $data,
            new Artist(
                owner: $request->getUser()->getLocalUser()
            )
        );
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        $record = parent::getRecord($request, $params);

        if ($record instanceof Artist) {
            $acl = $request->getAcl();
            if ($acl->canManageArtist($record->getIdRequired())) {
                return $record;
            }
        }

        return null;
    }
}
