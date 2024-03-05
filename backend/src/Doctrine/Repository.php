<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template TEntity as object
 */
class Repository
{
    /** @var class-string<TEntity> */
    protected string $entityClass;

    public function __construct(
        protected readonly EntityManagerInterface $em
    ) {
    }

    /**
     * @return ObjectRepository<TEntity>
     */
    public function getRepository(): ObjectRepository
    {
        return $this->em->getRepository($this->entityClass);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return TEntity|null
     */
    public function find(int|string $id): ?object
    {
        return $this->em->find($this->entityClass, $id);
    }

    /**
     * @return TEntity
     */
    public function requireRecord(int|string $id): object
    {
        $record = $this->find($id);
        if (null === $record) {
            throw new \InvalidArgumentException('Record not found.');
        }
        return $record;
    }
}
