<?php

namespace App\Entity\Repository;

use App\Doctrine\Repository;
use App\Entity\Artist;
use App\Entity\Enums\SocialTypes;
use App\Entity\Label;
use App\Entity\Social;

/**
 * @extends Repository<Social>
 */
final class SocialRepository extends Repository
{
    protected string $entityClass = Social::class;

    public function setSocialLinks(
        Artist|Label $relation,
        array $items = []
    ): void {
        $rawSocialItems = $this->findByRelation($relation);

        $socialItems = [];
        foreach ($rawSocialItems as $row) {
            $socialItems[$row->getId()] = $row;
        }

        foreach ($items as $item) {
            if (isset($item['id'], $socialItems[$item['id']])) {
                $record = $socialItems[$item['id']];
                unset($socialItems[$item['id']]);
            } else {
                $record = new Social($relation);
            }

            $record->setType(SocialTypes::from($item['type']));

            if ($record->getType() === SocialTypes::Custom) {
                $record->setName($item['name'] ?? '');
            }

            $record->setValue($item['value'] ?? '');
            $this->em->persist($record);
        }

        foreach ($socialItems as $row) {
            $this->em->remove($row);
        }

        $this->em->flush();
    }

    /**
     * @param Artist|Label $relation
     *
     * @return array<int, Social>
     */
    public function findByRelation(Artist|Label $relation): array
    {
        if ($relation instanceof Label) {
            return $this->getRepository()->findBy(['label' => $relation]);
        }

        return $this->getRepository()->findBy(['artist' => $relation]);
    }
}
