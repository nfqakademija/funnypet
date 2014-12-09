<?php

namespace NfqAkademija\FrontendBundle\Repositories;

use Doctrine\ORM\EntityRepository;

class PhotoRepository extends EntityRepository
{
    /**
     * @param $start
     * @return array|\NfqAkademija\FrontendBundle\Entity\Photo[]
     */
    public function findOrderByCreatedDate($start)
    {
        return $this->getEntityManager()
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->findBy(array(), array("createdDate" => "DESC"), 12, $start);
    }
}
