<?php

namespace App\Controller;

use Stringable;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @template TEntity as object
 */
abstract class AbstractEntityController
{
    public function __construct(
        protected Serializer $serializer,
        protected ValidatorInterface $validator
    ) {
    }

    /**
     * @param TEntity $record
     * @param array<string, mixed> $context
     *
     * @return array<mixed>
     */
    protected function toArray(object $record, array $context = []): array
    {
        return (array)$this->serializer->normalize(
            $record,
            null,
            array_merge(
                $context,
                [
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                    AbstractObjectNormalizer::MAX_DEPTH_HANDLER => fn($innerObject) => $this->displayShortenedObject(
                        $innerObject
                    ),
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn($object) => $this->displayShortenedObject(
                        $object
                    ),
                ]
            )
        );
    }

    /**
     * @param object $object
     *
     */
    protected function displayShortenedObject(object $object): mixed
    {
        if (method_exists($object, 'getName')) {
            return $object->getName();
        }

        if ($object instanceof Stringable) {
            return (string)$object;
        }

        return get_class($object) . ': ' . spl_object_hash($object);
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
        if (null !== $record) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $record;
        }

        return $this->serializer->denormalize($data, $this->entityClass, null, $context);
    }
}
