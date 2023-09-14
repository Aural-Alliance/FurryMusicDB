<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Profile\LabelsController as ProfileLabelsController;
use App\Entity\Label;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class LabelsController extends ProfileLabelsController
{
    protected string $entityClass = Label::class;
    protected string $resourceRouteName = 'api:admin:label';

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Label::class, 'e');

        return $this->buildList($qb, $request, $response, $params);
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        /** @var string $id */
        $id = $params['id'];

        return $this->em->find($this->entityClass, $id);
    }
}
