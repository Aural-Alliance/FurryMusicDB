<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class LabelArtistsController extends ArtistsController
{
    protected string $resourceRouteName = 'api:profile:label:artist';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $label = $request->getLabel();

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Artist::class, 'e')
            ->where('e.label = :label')
            ->setParameter('label', $label);

        return $this->buildList($qb, $request, $response, $params);
    }

    protected function createRecord(ServerRequest $request, array $data): object
    {
        return $this->editRecord(
            $data,
            new Artist(
                label: $request->getLabel()
            )
        );
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        /** @var string $id */
        $id = $params['id'];

        return $this->em->getRepository($this->entityClass)
            ->findOneBy([
                'id' => $id,
                'label' => $request->getLabel(),
            ]);
    }
}
