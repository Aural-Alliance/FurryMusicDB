<?php

namespace App\Controller\Profile;

use App\Avatars;
use App\Controller\AbstractCrudController;
use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Repository\SocialRepository;
use App\Exception\ValidationException;
use App\Serializer\ApiSerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractListingController extends AbstractCrudController
{
    public function __construct(
        EntityManagerInterface $em,
        ApiSerializerInterface $apiSerializer,
        ValidatorInterface $validator,
        protected readonly SocialRepository $socialRepo,
        protected readonly Avatars $avatars
    ) {
        parent::__construct($em, $apiSerializer, $validator);
    }

    protected function editRecord(?array $data, object $record = null, array $context = []): object
    {
        if (null === $data) {
            throw new \InvalidArgumentException('Could not parse input data.');
        }

        $avatar = $data['avatar'] ?? null;
        unset($data['avatar']);

        $socials = $data['socials'] ?? [];
        unset($data['socials']);

        /** @var Artist|Label $record */
        $record = $this->fromArray($data, $record, $context);

        $errors = $this->validator->validate($record);
        if (count($errors) > 0) {
            throw ValidationException::fromValidationErrors($errors);
        }

        $this->em->persist($record);
        $this->em->flush();

        if (null !== $socials) {
            $this->socialRepo->setSocialLinks($record, $socials);
        }

        if (null !== $avatar) {
            $this->avatars->upload(
                $avatar,
                $this->avatars->getPathForListing($record)
            );

            $record->setArtUpdated();
            $this->em->persist($record);
            $this->em->flush();
        }

        return $record;
    }
}
