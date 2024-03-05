<?php

declare(strict_types=1);

namespace App\Controller\Artists;

use App\Controller\Traits\CanSortResults;
use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Paginator;
use App\Serializer\ApiSerializerInterface;
use App\Utilities\Strings;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;

final class ListArtistsAction
{
    use CanSortResults;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApiSerializerInterface $apiSerializer,
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Artist::class, 'a');

        $query = $request->getQueryParams();
        if (!empty($query['search'])) {
            $qb->andWhere('a.name LIKE :search')
                ->setParameter('search', '%' . $query['search'] . '%');
        }

        $qb = $this->sortQueryBuilder(
            $request,
            $qb,
            [
                'name' => 'a.name',
            ],
            'a.name',
        );

        $paginator = Paginator::fromQueryBuilder($qb, $request);

        $paginator->setPostprocessor(
            fn(Artist $row) => $this->viewRecord($row)
        );

        return $paginator->write($response);
    }

    private function viewRecord(Artist $label): array
    {
        $record = $this->apiSerializer->toArray($label);

        return [
            'id' => $record['id'],
            'updated_at' => $record['updated_at'],
            'art_updated_at' => $record['art_updated_at'],
            'name' => $record['name'],
            'description' => Strings::truncateText($record['description'] ?? ''),
        ];
    }
}
