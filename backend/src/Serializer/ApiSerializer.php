<?php

namespace App\Serializer;

use App\Normalizer\DoctrineEntityNormalizer;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Stringable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ApiSerializer implements ApiSerializerInterface
{
    private readonly Serializer $parentSerializer;

    public function __construct(
        Reader $reader,
        EntityManagerInterface $em
    ) {
        $classMetaFactory = new ClassMetadataFactory(
            new AnnotationLoader($reader)
        );

        $normalizers = [
            new BackedEnumNormalizer(),
            new JsonSerializableNormalizer(),
            new DoctrineEntityNormalizer(
                $em,
                classMetadataFactory: $classMetaFactory
            ),
            new ObjectNormalizer(
                classMetadataFactory: $classMetaFactory
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->parentSerializer = new Serializer($normalizers, $encoders);
    }

    public function toArray(object $record, array $context = []): array
    {
        return (array)$this->parentSerializer->normalize(
            $record,
            null,
            array_merge(
                [
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                    AbstractObjectNormalizer::MAX_DEPTH_HANDLER => fn($innerObject) => $this->displayShortenedObject(
                        $innerObject
                    ),
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn($object) => $this->displayShortenedObject(
                        $object
                    ),
                ],
                $context
            )
        );
    }

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

    public function fromArray(
        array $data,
        string $className,
        ?object $record = null,
        array $context = []
    ): object {
        if (null !== $record) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $record;
        }

        return $this->parentSerializer->denormalize(
            $data,
            $className,
            null,
            $context
        );
    }
}
