<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Avatars;
use App\Controller\AbstractCrudController;
use App\Entity\Artist;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Serializer\ApiSerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistsController extends AbstractCrudController
{
    protected string $entityClass = Artist::class;
    protected string $resourceRouteName = 'api:profile:artist';

    public function __construct(
        EntityManagerInterface $em,
        ApiSerializerInterface $apiSerializer,
        ValidatorInterface $validator,
        protected readonly Avatars $avatars
    ) {
        parent::__construct($em, $apiSerializer, $validator);
    }

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(Artist::class, 'e')
            ->where('e.owner = :user')
            ->setParameter('user', $request->getUser());

        return $this->buildList(
            $qb,
            $request,
            $response,
            $params
        );
    }

    protected function buildList(
        QueryBuilder $qb,
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
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

    protected function editRecord(?array $data, ?object $record = null, array $context = []): object
    {
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);

        /** @var Artist $record */
        $record = parent::editRecord($data, $record, $context);

        if (null !== $avatar) {
            $this->avatars->upload(
                $avatar,
                $this->avatars->getArtistPath($record->getIdRequired())
            );

            $record->setArtUpdated();
            $this->em->persist($record);
            $this->em->flush();
        }

        return $record;
    }
}
