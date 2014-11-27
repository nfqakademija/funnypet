<?php

namespace NfqAkademija\FrontendBundle\Service;

use Doctrine\ORM\EntityManager;

class DisplayPhoto
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $doctrine
     */
    public function __construct(EntityManager $doctrine)
    {
        $this->entityManager = $doctrine;
    }

    /**
     * Get photos array
     * Used in dashboard and infinity scroll
     *
     * @param int $stat
     * @return array|\NfqAkademija\FrontendBundle\Entity\Photo[]
     */
    public function getDashboardPhotos($stat = 0)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->findBy(array(), array("createdDate" => "DESC"), 10, $stat);
    }

    /**
     * Get photo object
     *
     * @param integer $photo_id
     * @return \NfqAkademija\FrontendBundle\Entity\Photo
     */
    public function getPhoto($photo_id)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->find($photo_id);
    }
}
