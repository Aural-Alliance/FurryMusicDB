<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Api\Error;
use App\Entity\Api\Status;
use App\Exception\ValidationException;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Paginator;
use App\Serializer\ApiSerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @template TEntity as object
 */
abstract class AbstractCrudController
{
    /** @var class-string<TEntity> The fully-qualified (::class) class name of the entity being managed. */
    protected string $entityClass;

    /** @var string The route name used to generate the "self" links for each record. */
    protected string $resourceRouteName;

    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly ApiSerializerInterface $apiSerializer,
        protected readonly ValidatorInterface $validator
    ) {
    }

    public function listAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $query = $this->em->createQuery('SELECT e FROM ' . $this->entityClass . ' e');

        return $this->listPaginatedFromQuery($request, $response, $query);
    }

    protected function listPaginatedFromQuery(
        ServerRequest $request,
        Response $response,
        Query $query,
        callable $postProcessor = null
    ): ResponseInterface {
        $paginator = Paginator::fromQuery($query, $request);

        $postProcessor ??= fn($row) => $this->viewRecord($row, $request);
        $paginator->setPostprocessor($postProcessor);

        return $paginator->write($response);
    }

    public function createAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $row = $this->createRecord($request, (array)$request->getParsedBody());

        $return = $this->viewRecord($row, $request);
        return $response->withJson($return);
    }

    /**
     * @param array $data
     * @return TEntity
     */
    protected function createRecord(ServerRequest $request, array $data): object
    {
        return $this->editRecord($data);
    }

    public function getAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $record = $this->getRecord($request, $params);

        if (null === $record) {
            return $response->withStatus(404)
                ->withJson(Error::notFound());
        }

        $return = $this->viewRecord($record, $request);
        return $response->withJson($return);
    }

    /**
     * @return TEntity|null
     */
    protected function getRecord(ServerRequest $request, array $params): ?object
    {
        /** @var string $id */
        $id = $params['id'];

        return $this->em->find($this->entityClass, $id);
    }

    /**
     * @param TEntity $record
     * @param ServerRequest $request
     *
     */
    protected function viewRecord(object $record, ServerRequest $request): mixed
    {
        if (!($record instanceof $this->entityClass)) {
            throw new InvalidArgumentException(sprintf('Record must be an instance of %s.', $this->entityClass));
        }

        $return = $this->toArray($record);

        $isInternal = ('true' === $request->getParam('internal', 'false'));
        $router = $request->getRouter();

        if (method_exists($record, 'getId')) {
            $return['links'] = [
                'self' => $router->fromHere(
                    routeName: $this->resourceRouteName,
                    routeParams: ['id' => $record->getId()],
                    absolute: !$isInternal
                ),
            ];
        }

        return $return;
    }

    public function editAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $record = $this->getRecord($request, $params);

        if (null === $record) {
            return $response->withStatus(404)
                ->withJson(Error::notFound());
        }

        $this->editRecord((array)$request->getParsedBody(), $record);

        return $response->withJson(Status::updated());
    }

    /**
     * @param array<mixed>|null $data
     * @param TEntity|null $record
     * @param array<string, mixed> $context
     *
     * @return TEntity
     */
    protected function editRecord(?array $data, ?object $record = null, array $context = []): object
    {
        if (null === $data) {
            throw new InvalidArgumentException('Could not parse input data.');
        }

        $record = $this->fromArray($data, $record, $context);

        $errors = $this->validator->validate($record);
        if (count($errors) > 0) {
            throw ValidationException::fromValidationErrors($errors);
        }

        $this->em->persist($record);
        $this->em->flush();

        return $record;
    }

    public function deleteAction(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $record = $this->getRecord($request, $params);

        if (null === $record) {
            return $response->withStatus(404)
                ->withJson(Error::notFound());
        }

        $this->deleteRecord($record);

        return $response->withJson(Status::deleted());
    }

    /**
     * @param TEntity $record
     */
    protected function deleteRecord(object $record): void
    {
        if (!($record instanceof $this->entityClass)) {
            throw new InvalidArgumentException(sprintf('Record must be an instance of %s.', $this->entityClass));
        }

        $this->em->remove($record);
        $this->em->flush();
    }

    /**
     * @param TEntity $record
     * @param array<string, mixed> $context
     *
     * @return array<mixed>
     */
    protected function toArray(object $record, array $context = []): array
    {
        return $this->apiSerializer->toArray(
            $record,
            $context
        );
    }

    /**
     * @param array<mixed> $data
     * @param TEntity|null $record
     * @param array<string, mixed> $context
     *
     * @return TEntity
     */
    protected function fromArray(array $data, ?object $record = null, array $context = []): object
    {
        return $this->apiSerializer->fromArray(
            $data,
            $this->entityClass,
            $record,
            $context
        );
    }
}
