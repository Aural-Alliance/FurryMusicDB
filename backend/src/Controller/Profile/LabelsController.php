<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Avatars;
use App\Controller\AbstractCrudController;
use App\Entity\Label;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Serializer\ApiSerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LabelsController extends AbstractCrudController
{
    protected string $entityClass = Label::class;
    protected string $resourceRouteName = 'api:profile:label';

    public function __construct(
        EntityManagerInterface $em,
        ApiSerializerInterface $apiSerializer,
        ValidatorInterface $validator,
        private readonly Avatars $avatars
    ) {
        parent::__construct($em, $apiSerializer, $validator);
    }

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
        if (!($record instanceof Label)) {
            throw new \InvalidArgumentException(
                sprintf('Record must be an instance of %s.', Label::class)
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
        return $this->editRecord($data, null, [
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                $this->entityClass => [
                    'owner' => $request->getUser()->getLocalUser(),
                ],
            ],
        ]);
    }

    protected function editRecord(?array $data, ?object $record = null, array $context = []): object
    {
        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);

        /** @var Label $record */
        $record = parent::editRecord($data, $record, $context);

        if (null !== $avatar) {
            $this->avatars->upload(
                $avatar,
                $this->avatars->getLabelPath($record->getIdRequired())
            );

            $record->setArtUpdated();
            $this->em->persist($record);
            $this->em->flush();
        }

        return $record;
    }

    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        $record = parent::getRecord($request, $params);

        if ($record instanceof Label) {
            $acl = $request->getAcl();
            if ($acl->canManageLabel($record->getIdRequired())) {
                return $record;
            }
        }

        throw new \InvalidArgumentException('Record not found.');
    }
}
