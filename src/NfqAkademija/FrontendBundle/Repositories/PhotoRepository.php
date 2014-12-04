<?php

namespace NfqAkademija\FrontendBundle\Repositories;

use Doctrine\ORM\EntityRepository;

class PhotoRepository extends EntityRepository
{
    public function findOrderByCreatedDate($start)
    {
        return $this->getEntityManager()
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->findBy(array(), array("createdDate" => "DESC"), 12, $start);
    }
}
