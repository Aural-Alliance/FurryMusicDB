<?php

namespace App\Serializer;

interface ApiSerializerInterface
{
    /**
     * @template TEntity of object
     * @param TEntity $record
     * @param array<string, mixed> $context
     *
     * @return array<mixed>
     */
    public function toArray(object $record, array $context = []): array;

    /**
     * @template TEntity of object
     * @param array<mixed> $data
     * @param class-string<TEntity> $className
     * @param TEntity|null $record
     * @param array<string, mixed> $context
     *
     * @return TEntity
     */
    public function fromArray(
        array $data,
        string $className,
        ?object $record = null,
        array $context = []
    ): object;
}
