<?php

declare(strict_types=1);

namespace App\Controller\Labels;

use App\Entity\Label;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Serializer\ApiSerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;

final class GetLabelAction
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApiSerializerInterface $apiSerializer
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $record = $this->em->find(
            Label::class,
            $params['label_id']
        );

        if (!($record instanceof Label)) {
            throw new \InvalidArgumentException('Record not found!');
        }

        return $response->withJson(
            $this->apiSerializer->toArray($record)
        );
    }
}
