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

    public function getDashboardPhotos($stat = 0)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->findBy(array(), array("createdDate" => "DESC"), 10, $stat);
    }
}
