<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Label;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class LabelsController extends AbstractCrudController
{
    protected string $entityClass = Label::class;
    protected string $resourceRouteName = 'api:label';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $acl = $request->getAcl();

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Label::class, 'e');

        if (!$acl->isAdministrator()) {
            $qb->where('e.owner = :user')
                ->setParameter('user', $request->getUser());
        }

        return $this->listPaginatedFromQuery($request, $response, $qb->getQuery());
    }

    protected function viewRecord(object $record, ServerRequest $request): array
    {
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

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        $record = parent::getRecord($request, $params);

        if ($record instanceof Label) {
            $acl = $request->getAcl();
            if ($acl->canManageLabel($record)) {
                return $record;
            }
        }

        throw new \InvalidArgumentException('Record not found.');
    }
}
